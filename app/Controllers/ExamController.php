<?php

namespace App\Controllers;

use App\Models\ExamModel;
use App\Models\QuestionModel;
use App\Models\ExamSessionModel;
use App\Models\UserAnswerModel;
use App\Models\TabSwitchLogModel;

class ExamController extends BaseController
{
    protected $examModel;
    protected $questionModel;
    protected $sessionModel;
    protected $answerModel;
    protected $tabSwitchModel;

    public function __construct()
    {
        $this->examModel = new ExamModel();
        $this->questionModel = new QuestionModel();
        $this->sessionModel = new ExamSessionModel();
        $this->answerModel = new UserAnswerModel();
        $this->tabSwitchModel = new TabSwitchLogModel();
    }

    /**
     * Display instructions page before starting exam
     */
    public function instructions($examId)
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->to('/login');
        }

        $exam = $this->examModel->getExamWithSubjects($examId);
        if (!$exam) {
            return redirect()->to('/dashboard')->with('error', 'Exam not found');
        }

        // Check if user already has an active session
        $activeSession = $this->sessionModel->getActiveSession($user->id, $examId);
        if ($activeSession) {
            return redirect()->to('/exam/take/' . $activeSession->id);
        }

        $data = [
            'title' => 'Exam Instructions',
            'exam' => $exam
        ];

        return view('exam/instructions', $data);
    }

    /**
     * Start the exam - create session and redirect to exam page
     */
    public function start($examId)
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->to('/login');
        }

        $exam = $this->examModel->find($examId);
        if (!$exam) {
            return redirect()->to('/dashboard')->with('error', 'Exam not found');
        }

        // REQUIREMENT 3: Check if user has already attempted this exam (one attempt only)
        $previousAttempt = $this->sessionModel
            ->where('user_id', $user->id)
            ->where('exam_id', $examId)
            ->whereIn('status', ['completed', 'terminated'])
            ->first();

        if ($previousAttempt) {
            return redirect()->to('/dashboard')
                ->with('error', 'You have already attempted this exam. Only one attempt is allowed.');
        }

        // Check for existing active session
        $activeSession = $this->sessionModel->getActiveSession($user->id, $examId);
        if ($activeSession) {
            // Check if it's expired
            if ($this->sessionModel->isSessionExpired($activeSession->id)) {
                // Mark old session as expired
                $this->sessionModel->update($activeSession->id, ['status' => 'expired']);
            } else {
                // Resume active session
                return redirect()->to('/exam/take/' . $activeSession->id);
            }
        }

        // Expire any other old in_progress sessions for this user and exam
        $this->sessionModel->where('user_id', $user->id)
            ->where('exam_id', $examId)
            ->where('status', 'in_progress')
            ->set(['status' => 'expired'])
            ->update();

        // REQUIREMENT 4: Calculate adjusted duration for late joiners
        $startTime = date('Y-m-d H:i:s');
        $durationMinutes = $exam->duration_minutes;

        // If exam is scheduled, check if user is joining late
        if ($exam->is_scheduled && $exam->scheduled_start_time) {
            $now = new \DateTime('now', new \DateTimeZone('Asia/Kolkata'));
            $examStartTime = new \DateTime($exam->scheduled_start_time, new \DateTimeZone('Asia/Kolkata'));
            $examEndTime = new \DateTime($exam->scheduled_end_time, new \DateTimeZone('Asia/Kolkata'));

            // If user is joining after exam started, reduce duration
            if ($now > $examStartTime) {
                $lateByMinutes = ($now->getTimestamp() - $examStartTime->getTimestamp()) / 60;
                $remainingMinutes = $durationMinutes - $lateByMinutes;

                // Ensure at least 1 minute remaining
                if ($remainingMinutes <= 0) {
                    return redirect()->to('/dashboard')
                        ->with('error', 'Exam time has expired. You cannot join anymore.');
                }

                $durationMinutes = max(1, floor($remainingMinutes));
            }
        }

        $endTime = date('Y-m-d H:i:s', strtotime("+{$durationMinutes} minutes"));

        $sessionData = [
            'user_id' => $user->id,
            'exam_id' => $examId,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'status' => 'in_progress',
            'tab_switch_count' => 0,
            'unanswered' => $exam->total_questions
        ];

        $sessionId = $this->sessionModel->insert($sessionData);

        return redirect()->to('/exam/take/' . $sessionId);
    }

    /**
     * Display exam page with all questions
     */
    public function take($sessionId)
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->to('/login');
        }

        $session = $this->sessionModel->getSessionWithExam($sessionId);

        if (!$session || $session->user_id != $user->id) {
            return redirect()->to('/dashboard')->with('error', 'Invalid session');
        }

        // Check if session is expired or completed
        if ($session->status !== 'in_progress') {
            return redirect()->to('/exam/result/' . $sessionId);
        }

        if ($this->sessionModel->isSessionExpired($sessionId)) {
            $this->submitExam($sessionId, 'time_expired');
            return redirect()->to('/exam/result/' . $sessionId)->with('warning', 'Exam time expired');
        }

        // Get questions with options
        $questions = $this->questionModel->getQuestionsForExam(
            $session->exam_id,
            $session->exam->randomize_questions
        );

        // Get already answered questions with their selected options
        $answeredData = $this->answerModel->getSessionAnswers($sessionId);
        $answeredMap = [];
        foreach ($answeredData as $answer) {
            $answeredMap[$answer->question_id] = $answer->selected_option_id;
        }

        $data = [
            'title' => $session->exam->title,
            'session' => $session,
            'exam' => $session->exam,
            'questions' => $questions,
            'answeredMap' => $answeredMap
        ];

        return view('exam/take', $data);
    }

    /**
     * Save answer via AJAX
     */
    public function saveAnswer()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        $user = auth()->user();
        if (!$user) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $sessionId = $this->request->getPost('session_id');
        $questionId = $this->request->getPost('question_id');
        $optionId = $this->request->getPost('option_id');

        // Verify session belongs to user
        $session = $this->sessionModel->find($sessionId);
        if (!$session || $session->user_id != $user->id || $session->status !== 'in_progress') {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid session']);
        }

        // Check if session expired
        if ($this->sessionModel->isSessionExpired($sessionId)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Session expired', 'expired' => true]);
        }

        // Save answer
        $saved = $this->answerModel->saveAnswer($sessionId, $questionId, $optionId);

        if ($saved) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Answer saved successfully'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to save answer'
            ]);
        }
    }

    /**
     * Clear answer via AJAX
     */
    public function clearAnswer()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        $user = auth()->user();
        if (!$user) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $sessionId = $this->request->getPost('session_id');
        $questionId = $this->request->getPost('question_id');

        // Verify session
        $session = $this->sessionModel->find($sessionId);
        if (!$session || $session->user_id != $user->id) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid session']);
        }

        // Delete answer
        $deleted = $this->answerModel->where('session_id', $sessionId)
            ->where('question_id', $questionId)
            ->delete();

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Answer cleared'
        ]);
    }

    /**
     * Log tab switch via AJAX
     */
    public function logTabSwitch()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        $user = auth()->user();
        if (!$user) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $sessionId = $this->request->getPost('session_id');

        // Verify session
        $session = $this->sessionModel->find($sessionId);
        if (!$session || $session->user_id != $user->id || $session->status !== 'in_progress') {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid session']);
        }

        $exam = $this->examModel->find($session->exam_id);

        // Log the switch
        $this->tabSwitchModel->logSwitch($sessionId);

        // Increment counter
        $newCount = $session->tab_switch_count + 1;
        $this->sessionModel->update($sessionId, ['tab_switch_count' => $newCount]);

        $remaining = $exam->max_tab_switches_allowed - $newCount;

        // Check if limit exceeded
        if ($exam->prevent_tab_switch && $newCount >= $exam->max_tab_switches_allowed) {
            // Auto-submit exam
            $this->submitExam($sessionId, 'tab_switch_limit');

            // REQUIREMENT 1: Redirect to dashboard instead of result page
            return $this->response->setJSON([
                'success' => true,
                'terminate' => true,
                'message' => 'Tab switch limit exceeded. Exam submitted automatically.',
                'redirect' => '/dashboard'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'remaining' => max(0, $remaining),
            'message' => "Warning: You have {$remaining} tab switch(es) remaining"
        ]);
    }

    /**
     * Submit exam and calculate results
     * REQUIREMENT 1: Redirect to dashboard after submission
     */
    public function submit()
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->to('/login');
        }

        $sessionId = $this->request->getPost('session_id');

        // Verify session
        $session = $this->sessionModel->find($sessionId);
        if (!$session || $session->user_id != $user->id) {
            return redirect()->to('/dashboard')->with('error', 'Invalid session');
        }

        $this->submitExam($sessionId);

        // Redirect to feedback form after exam submission
        return redirect()->to('/exam/feedback/' . $sessionId);
    }

    /**
     * Internal method to submit exam and calculate score with transaction support
     */
    private function submitExam($sessionId, $reason = null)
    {
        $session = $this->sessionModel->find($sessionId);
        if (!$session || $session->status !== 'in_progress') {
            return false;
        }

        $db = \Config\Database::connect();

        // Start transaction
        $db->transStart();

        try {
            $exam = $this->examModel->find($session->exam_id);
            $stats = $this->answerModel->calculateSessionStats($sessionId);

            $unanswered = $exam->total_questions - $stats->attempted;
            $rawScore = $stats->correct * $exam->marks_per_question;

            $finalScore = $rawScore;
            if ($exam->has_negative_marking) {
                $penalty = $stats->wrong * $exam->negative_marks_per_question;
                $finalScore = $rawScore - $penalty;
            }

            $maxScore = $exam->total_questions * $exam->marks_per_question;
            $percentage = ($finalScore / $maxScore) * 100;

            $updateData = [
                'status' => $reason ? 'terminated' : 'completed',
                'actual_submit_time' => date('Y-m-d H:i:s'),
                'total_questions_attempted' => $stats->attempted,
                'correct_answers' => $stats->correct,
                'wrong_answers' => $stats->wrong,
                'unanswered' => $unanswered,
                'raw_score' => $rawScore,
                'final_score' => $finalScore,
                'percentage' => $percentage
            ];

            if ($reason) {
                $updateData['terminated_reason'] = $reason;
            }

            $this->sessionModel->update($sessionId, $updateData);

            // Calculate subject-wise results
            $this->calculateSubjectWiseResults($sessionId);

            // Complete transaction
            $db->transComplete();

            // Check transaction status
            if ($db->transStatus() === false) {
                log_message('error', 'Transaction failed in submitExam for session: ' . $sessionId);
                return false;
            }

            return true;
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Error submitting exam: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Calculate subject-wise performance
     */
    private function calculateSubjectWiseResults($sessionId)
    {
        $db = \Config\Database::connect();
        $session = $this->sessionModel->find($sessionId);
        $exam = $this->examModel->find($session->exam_id);

        // Get subject distribution
        $distribution = $db->table('exam_subject_distribution')
            ->where('exam_id', $session->exam_id)
            ->get()
            ->getResult();

        foreach ($distribution as $dist) {
            // Get all questions for this subject in this session
            $questionsInSubject = $db->table('user_answers ua')
                ->select('ua.*, q.subject_id')
                ->join('questions q', 'q.id = ua.question_id')
                ->where('ua.session_id', $sessionId)
                ->where('q.subject_id', $dist->subject_id)
                ->get()
                ->getResult();

            $correct = 0;
            $wrong = 0;
            $attempted = count($questionsInSubject);

            foreach ($questionsInSubject as $ans) {
                if ($ans->is_correct == 1) {
                    $correct++;
                } else {
                    $wrong++;
                }
            }

            $unanswered = $dist->number_of_questions - $attempted;
            $scoreObtained = $correct * $exam->marks_per_question;

            if ($exam->has_negative_marking) {
                $scoreObtained -= ($wrong * $exam->negative_marks_per_question);
            }

            $resultData = [
                'session_id' => $sessionId,
                'subject_id' => $dist->subject_id,
                'total_questions_in_subject' => $dist->number_of_questions,
                'correct_answers' => $correct,
                'wrong_answers' => $wrong,
                'unanswered' => $unanswered,
                'score_obtained' => $scoreObtained
            ];

            $db->table('exam_results')->insert($resultData);
        }
    }

    /**
     * Display exam result
     */
    public function result($sessionId)
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->to('/login');
        }

        $session = $this->sessionModel->getSessionWithExam($sessionId);

        if (!$session || $session->user_id != $user->id) {
            return redirect()->to('/dashboard')->with('error', 'Invalid session');
        }

        if ($session->status === 'in_progress') {
            return redirect()->to('/exam/take/' . $sessionId);
        }

        // Get subject-wise results
        $db = \Config\Database::connect();
        $subjectResults = $db->table('exam_results er')
            ->select('er.*, s.name as subject_name, s.code as subject_code')
            ->join('subjects s', 's.id = er.subject_id')
            ->where('er.session_id', $sessionId)
            ->get()
            ->getResult();

        $data = [
            'title' => 'Exam Result',
            'session' => $session,
            'exam' => $session->exam,
            'subjectResults' => $subjectResults
        ];

        return view('exam/result', $data);
    }

    /**
     * Display feedback form
     */
    public function feedback($sessionId)
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->to('/login');
        }

        $session = $this->sessionModel->getSessionWithExam($sessionId);

        if (!$session || $session->user_id != $user->id) {
            return redirect()->to('/dashboard')->with('error', 'Invalid session');
        }

        if ($session->status === 'in_progress') {
            return redirect()->to('/exam/take/' . $sessionId);
        }

        // Check if feedback already submitted
        $feedbackModel = model('ExamFeedbackModel');
        if ($feedbackModel->hasFeedback($sessionId)) {
            return redirect()->to('/dashboard')
                ->with('message', 'Thank you! You have already submitted feedback for this exam.');
        }

        $data = [
            'title' => 'Exam Feedback',
            'session' => $session,
            'exam' => $session->exam,
        ];

        return view('exam/feedback', $data);
    }

    /**
     * Submit feedback
     */
    public function submitFeedback()
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->to('/login');
        }

        $feedbackModel = model('ExamFeedbackModel');

        // Validate input
        $rules = [
            'session_id'                => 'required|integer',
            'exam_id'                   => 'required|integer',
            'overall_experience_rating' => 'required|integer|greater_than[0]|less_than[11]',
            'web_panel_experience'      => 'required|in_list[poor,below_average,average,good,excellent]',
            'question_quality'          => 'required|in_list[poor,below_average,average,good,excellent]',
            'will_refer_friends'        => 'required|in_list[0,1]',
            'interested_next_test'      => 'required|in_list[0,1]',
            'felt_same_pressure'        => 'required|in_list[yes,no,maybe]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Collect feedback data
        $feedbackData = [
            'session_id'                => $this->request->getPost('session_id'),
            'user_id'                   => $user->id,
            'exam_id'                   => $this->request->getPost('exam_id'),
            'overall_experience_rating' => $this->request->getPost('overall_experience_rating'),
            'web_panel_experience'      => $this->request->getPost('web_panel_experience'),
            'question_quality'          => $this->request->getPost('question_quality'),
            'will_refer_friends'        => $this->request->getPost('will_refer_friends'),
            'interested_next_test'      => $this->request->getPost('interested_next_test'),
            'real_vs_mock_difference'   => $this->request->getPost('real_vs_mock_difference'),
            'general_feedback'          => $this->request->getPost('general_feedback'),
            'felt_same_pressure'        => $this->request->getPost('felt_same_pressure'),
            'other_test_series'         => $this->request->getPost('other_test_series'),
            'willing_to_pay'            => $this->request->getPost('willing_to_pay'),
            'amount_paid_range'         => $this->request->getPost('amount_paid_range'),
        ];

        if ($feedbackModel->save($feedbackData)) {
            return redirect()->to('/dashboard')
                ->with('success', 'Thank you for your valuable feedback! 🎉');
        } else {
            return redirect()->back()->withInput()
                ->with('errors', $feedbackModel->errors());
        }
    }

    /**
     * Get remaining time via AJAX
     */
    public function getRemainingTime()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false]);
        }

        $sessionId = $this->request->getPost('session_id');
        $session = $this->sessionModel->find($sessionId);

        if (!$session) {
            return $this->response->setJSON(['success' => false]);
        }

        $endTime = strtotime($session->end_time);
        $currentTime = time();
        $remaining = max(0, $endTime - $currentTime);

        return $this->response->setJSON([
            'success' => true,
            'remaining' => $remaining,
            'expired' => $remaining <= 0
        ]);
    }

    /**
     * Switch exam language
     */
    public function switchLanguage()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $language = $this->request->getPost('language');

        if (in_array($language, ['english', 'marathi'])) {
            setLanguage($language);
            return $this->response->setJSON([
                'success' => true,
                'language' => $language
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Invalid language'
        ]);
    }
}
