<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ExamModel;
use App\Models\SubjectModel;
use App\Models\QuestionModel;

class ExamAdminController extends BaseController
{
    protected $examModel;
    protected $subjectModel;
    protected $questionModel;

    public function __construct()
    {
        $this->examModel = new ExamModel();
        $this->subjectModel = new SubjectModel();
        $this->questionModel = new QuestionModel();
        helper('text'); // Load text helper
    }

    public function index()
    {
        if (!auth()->user()->can('exams.create') && !auth()->user()->can('exams.schedule')) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $exams = $this->examModel->select('exams.*, users.username as creator_name')
            ->join('users', 'users.id = exams.created_by', 'left')
            ->orderBy('exams.created_at', 'DESC')
            ->findAll();

        $data = [
            'title' => 'Manage Exams',
            'exams' => $exams
        ];

        return view('admin/exams/index', $data);
    }

    public function create()
    {
        if (!auth()->user()->can('exams.create')) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $subjects = $this->subjectModel->orderBy('name', 'ASC')->findAll();

        // Get question counts per subject
        $subjectsWithCounts = [];
        foreach ($subjects as $subject) {
            $count = $this->questionModel->where('subject_id', $subject->id)->countAllResults(false);
            $subject->question_count = $count;
            $subjectsWithCounts[] = $subject;
        }

        $data = [
            'title' => 'Create Exam',
            'subjects' => $subjectsWithCounts
        ];

        return view('admin/exams/create', $data);
    }

    public function store()
    {
        if (!auth()->user()->can('exams.create')) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $rules = [
            'title' => 'required|min_length[5]|max_length[200]',
            'description' => 'permit_empty|max_length[500]',
            'duration_minutes' => 'required|is_natural_no_zero|greater_than[0]',
            'marks_per_question' => 'required|decimal|greater_than[0]',
            'has_negative_marking' => 'required|in_list[0,1]',
            'negative_marks_per_question' => 'permit_empty|decimal',
            'max_tab_switches_allowed' => 'required|is_natural',
            'randomize_questions' => 'required|in_list[0,1]',
            'randomize_options' => 'required|in_list[0,1]',
            'subjects' => 'required',
            'questions_count' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $subjects = $this->request->getPost('subjects');
        $questionsCounts = $this->request->getPost('questions_count');

        if (!is_array($subjects) || empty($subjects)) {
            return redirect()->back()->withInput()->with('error', 'Please select at least one subject');
        }

        // Validate total questions
        $totalQuestions = 0;
        foreach ($subjects as $index => $subjectId) {
            $count = (int)($questionsCounts[$index] ?? 0);
            if ($count <= 0) {
                return redirect()->back()->withInput()->with('error', 'All subjects must have at least 1 question');
            }
            $totalQuestions += $count;
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Create exam
            $examData = [
                'title' => $this->request->getPost('title'),
                'description' => $this->request->getPost('description'),
                'duration_minutes' => $this->request->getPost('duration_minutes'),
                'total_questions' => $totalQuestions,
                'marks_per_question' => $this->request->getPost('marks_per_question'),
                'has_negative_marking' => $this->request->getPost('has_negative_marking'),
                'negative_marks_per_question' => $this->request->getPost('negative_marks_per_question') ?? 0,
                'max_tab_switches_allowed' => $this->request->getPost('max_tab_switches_allowed'),
                'randomize_questions' => $this->request->getPost('randomize_questions'),
                'randomize_options' => $this->request->getPost('randomize_options'),
                'status' => 'active', // Set to 'active' so exam shows on dashboard immediately
                'created_by' => auth()->user()->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $examId = $this->examModel->insert($examData);

            if (!$examId) {
                throw new \Exception('Failed to create exam');
            }

            // Insert subject distribution
            foreach ($subjects as $index => $subjectId) {
                $count = (int)$questionsCounts[$index];

                $distData = [
                    'exam_id' => $examId,
                    'subject_id' => $subjectId,
                    'number_of_questions' => $count,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                $db->table('exam_subject_distribution')->insert($distData);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }

            return redirect()->to('/admin/exams')->with('success', 'Exam created successfully. You can now schedule it.');

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Error creating exam: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to create exam: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        if (!auth()->user()->can('exams.create')) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $exam = $this->examModel->find($id);

        if (!$exam) {
            return redirect()->to('/admin/exams')->with('error', 'Exam not found');
        }

        $subjects = $this->subjectModel->orderBy('name', 'ASC')->findAll();

        // Get question counts per subject
        $subjectsWithCounts = [];
        foreach ($subjects as $subject) {
            $count = $this->questionModel->where('subject_id', $subject->id)->countAllResults(false);
            $subject->question_count = $count;
            $subjectsWithCounts[] = $subject;
        }

        // Get current distribution
        $distribution = \Config\Database::connect()
            ->table('exam_subject_distribution')
            ->where('exam_id', $id)
            ->get()
            ->getResult();

        $data = [
            'title' => 'Edit Exam',
            'exam' => $exam,
            'subjects' => $subjectsWithCounts,
            'distribution' => $distribution
        ];

        return view('admin/exams/edit', $data);
    }

    public function update($id)
    {
        if (!auth()->user()->can('exams.create')) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $exam = $this->examModel->find($id);

        if (!$exam) {
            return redirect()->to('/admin/exams')->with('error', 'Exam not found');
        }

        $rules = [
            'title' => 'required|min_length[5]|max_length[200]',
            'description' => 'permit_empty|max_length[500]',
            'duration_minutes' => 'required|is_natural_no_zero|greater_than[0]',
            'marks_per_question' => 'required|decimal|greater_than[0]',
            'has_negative_marking' => 'required|in_list[0,1]',
            'negative_marks_per_question' => 'permit_empty|decimal',
            'max_tab_switches_allowed' => 'required|is_natural',
            'randomize_questions' => 'required|in_list[0,1]',
            'randomize_options' => 'required|in_list[0,1]',
            'subjects' => 'required',
            'questions_count' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $subjects = $this->request->getPost('subjects');
        $questionsCounts = $this->request->getPost('questions_count');

        if (!is_array($subjects) || empty($subjects)) {
            return redirect()->back()->withInput()->with('error', 'Please select at least one subject');
        }

        // Validate total questions
        $totalQuestions = 0;
        foreach ($subjects as $index => $subjectId) {
            $count = (int)($questionsCounts[$index] ?? 0);
            if ($count <= 0) {
                return redirect()->back()->withInput()->with('error', 'All subjects must have at least 1 question');
            }
            $totalQuestions += $count;
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Update exam
            $examData = [
                'title' => $this->request->getPost('title'),
                'description' => $this->request->getPost('description'),
                'duration_minutes' => $this->request->getPost('duration_minutes'),
                'total_questions' => $totalQuestions,
                'marks_per_question' => $this->request->getPost('marks_per_question'),
                'has_negative_marking' => $this->request->getPost('has_negative_marking'),
                'negative_marks_per_question' => $this->request->getPost('negative_marks_per_question') ?? 0,
                'max_tab_switches_allowed' => $this->request->getPost('max_tab_switches_allowed'),
                'randomize_questions' => $this->request->getPost('randomize_questions'),
                'randomize_options' => $this->request->getPost('randomize_options'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $this->examModel->update($id, $examData);

            // Delete old distribution
            $db->table('exam_subject_distribution')->where('exam_id', $id)->delete();

            // Insert new distribution
            foreach ($subjects as $index => $subjectId) {
                $count = (int)$questionsCounts[$index];

                $distData = [
                    'exam_id' => $id,
                    'subject_id' => $subjectId,
                    'number_of_questions' => $count,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                $db->table('exam_subject_distribution')->insert($distData);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }

            return redirect()->to('/admin/exams')->with('success', 'Exam updated successfully');

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Error updating exam: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to update exam: ' . $e->getMessage());
        }
    }

    public function schedule($id)
    {
        if (!auth()->user()->can('exams.schedule')) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $exam = $this->examModel->find($id);

        if (!$exam) {
            return redirect()->to('/admin/exams')->with('error', 'Exam not found');
        }

        $data = [
            'title' => 'Schedule Exam',
            'exam' => $exam
        ];

        return view('admin/exams/schedule', $data);
    }

    public function updateSchedule($id)
    {
        if (!auth()->user()->can('exams.schedule')) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $exam = $this->examModel->find($id);

        if (!$exam) {
            return redirect()->to('/admin/exams')->with('error', 'Exam not found');
        }

        $rules = [
            'scheduled_start_time' => 'required|valid_date[Y-m-d\TH:i]',
            'scheduled_end_time' => 'required|valid_date[Y-m-d\TH:i]',
            'status' => 'required|in_list[draft,scheduled,active,completed,archived]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $startTime = $this->request->getPost('scheduled_start_time');
        $endTime = $this->request->getPost('scheduled_end_time');

        // Convert to datetime format
        $startDateTime = date('Y-m-d H:i:s', strtotime($startTime));
        $endDateTime = date('Y-m-d H:i:s', strtotime($endTime));

        // Validate end time is after start time
        if (strtotime($endDateTime) <= strtotime($startDateTime)) {
            return redirect()->back()->withInput()->with('error', 'End time must be after start time');
        }

        $updateData = [
            'scheduled_start_time' => $startDateTime,
            'scheduled_end_time' => $endDateTime,
            'is_scheduled' => 1,
            'status' => $this->request->getPost('status'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($this->examModel->update($id, $updateData)) {
            return redirect()->to('/admin/exams')->with('success', 'Exam scheduled successfully');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to schedule exam');
        }
    }

    public function delete($id)
    {
        if (!auth()->user()->can('exams.manage')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $exam = $this->examModel->find($id);

        if (!$exam) {
            return $this->response->setJSON(['success' => false, 'message' => 'Exam not found']);
        }

        // Check if exam has sessions
        $db = \Config\Database::connect();
        $sessionCount = $db->table('exam_sessions')->where('exam_id', $id)->countAllResults();

        if ($sessionCount > 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => "Cannot delete exam. It has {$sessionCount} exam session(s) associated with it."
            ]);
        }

        $db->transStart();

        try {
            // Delete subject distribution
            $db->table('exam_subject_distribution')->where('exam_id', $id)->delete();

            // Delete exam
            $this->examModel->delete($id);

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }

            return $this->response->setJSON(['success' => true, 'message' => 'Exam deleted successfully']);

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Error deleting exam: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete exam']);
        }
    }
}
