<?php

namespace App\Models;

use CodeIgniter\Model;

class QuestionModel extends Model
{
    protected $table = 'questions';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'subject_id',
        'question_text',
        'question_text_marathi',
        'question_type',
        'question_image_path',
        'question_image',
        'explanation',
        'explanation_marathi',
        'difficulty_level'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Get questions with options for an exam (optimized to reduce N+1 queries)
     */
    public function getQuestionsForExam($examId, $randomize = true)
    {
        $db = \Config\Database::connect();

        // Get exam subject distribution
        $distribution = $db->table('exam_subject_distribution')
            ->where('exam_id', $examId)
            ->get()
            ->getResult();

        // First, collect all subjects data in one query
        $subjectIds = array_column($distribution, 'subject_id');
        $subjects = $db->table('subjects')
            ->whereIn('id', $subjectIds)
            ->get()
            ->getResult();

        // Create subject lookup map
        $subjectMap = [];
        foreach ($subjects as $subject) {
            $subjectMap[$subject->id] = $subject;
        }

        $allQuestions = [];
        $allQuestionIds = [];

        // Collect questions for all subjects
        foreach ($distribution as $dist) {
            // Get questions for each subject
            $builder = $db->table('questions');
            $builder->where('subject_id', $dist->subject_id);
            if ($randomize) {
                $builder->orderBy('RAND()');
            }
            $builder->limit($dist->number_of_questions);
            $questions = $builder->get()->getResult();

            foreach ($questions as $question) {
                // Attach subject info from map
                $question->subject = $subjectMap[$question->subject_id] ?? null;
                $question->options = []; // Will be filled later
                $allQuestions[] = $question;
                $allQuestionIds[] = $question->id;
            }
        }

        // Now fetch ALL options for ALL questions in a single query
        if (!empty($allQuestionIds)) {
            $optionsQuery = $db->table('options')
                ->whereIn('question_id', $allQuestionIds);

            if ($randomize) {
                // For randomization, we'll handle it per question after fetching
                $optionsQuery->orderBy('question_id, RAND()');
            } else {
                $optionsQuery->orderBy('question_id, display_order', 'ASC');
            }

            $allOptions = $optionsQuery->get()->getResult();

            // Group options by question_id
            $optionsMap = [];
            foreach ($allOptions as $option) {
                if (!isset($optionsMap[$option->question_id])) {
                    $optionsMap[$option->question_id] = [];
                }
                $optionsMap[$option->question_id][] = $option;
            }

            // Attach options to questions
            foreach ($allQuestions as $question) {
                $question->options = $optionsMap[$question->id] ?? [];

                // If randomize is true, shuffle options for each question
                if ($randomize && !empty($question->options)) {
                    shuffle($question->options);
                }
            }
        }

        // Randomize overall question order if required
        if ($randomize) {
            shuffle($allQuestions);
        }

        return $allQuestions;
    }

    /**
     * Get question with options by ID
     */
    public function getQuestionWithOptions($questionId)
    {
        $question = $this->find($questionId);
        if (!$question) {
            return null;
        }

        $db = \Config\Database::connect();
        $question->options = $db->table('options')
            ->where('question_id', $questionId)
            ->orderBy('display_order', 'ASC')
            ->get()
            ->getResult();

        return $question;
    }
}
