<?php

namespace App\Models;

use CodeIgniter\Model;

class UserAnswerModel extends Model
{
    protected $table = 'user_answers';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'session_id',
        'question_id',
        'selected_option_id',
        'is_correct',
        'answered_at',
        'created_at',
        'updated_at'
    ];
    protected $useTimestamps = false;

    /**
     * Save or update answer with transaction support
     */
    public function saveAnswer($sessionId, $questionId, $optionId)
    {
        $db = \Config\Database::connect();

        // Start transaction
        $db->transStart();

        try {
            // Check if option is correct
            $option = $db->table('options')->where('id', $optionId)->get()->getRow();
            $isCorrect = $option ? $option->is_correct : 0;

            // Check if answer already exists
            $existing = $this->where('session_id', $sessionId)
                ->where('question_id', $questionId)
                ->first();

            $data = [
                'session_id' => $sessionId,
                'question_id' => $questionId,
                'selected_option_id' => $optionId,
                'is_correct' => $isCorrect,
                'answered_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            if ($existing) {
                $result = $this->update($existing->id, $data);
            } else {
                $data['created_at'] = date('Y-m-d H:i:s');
                $result = $this->insert($data);
            }

            // Complete transaction
            $db->transComplete();

            // Check transaction status
            if ($db->transStatus() === false) {
                return false;
            }

            return $result;
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Error saving answer: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all answers for a session
     */
    public function getSessionAnswers($sessionId)
    {
        return $this->where('session_id', $sessionId)->findAll();
    }

    /**
     * Get answered question IDs for session
     */
    public function getAnsweredQuestionIds($sessionId)
    {
        $answers = $this->where('session_id', $sessionId)->findAll();
        return array_column($answers, 'question_id');
    }

    /**
     * Calculate session statistics
     */
    public function calculateSessionStats($sessionId)
    {
        $db = \Config\Database::connect();

        $correct = $this->where('session_id', $sessionId)
            ->where('is_correct', 1)
            ->countAllResults();

        $wrong = $this->where('session_id', $sessionId)
            ->where('is_correct', 0)
            ->where('selected_option_id IS NOT NULL')
            ->countAllResults();

        $attempted = $this->where('session_id', $sessionId)
            ->countAllResults();

        return (object)[
            'correct' => $correct,
            'wrong' => $wrong,
            'attempted' => $attempted
        ];
    }
}
