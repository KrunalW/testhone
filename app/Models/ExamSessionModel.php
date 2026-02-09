<?php

namespace App\Models;

use CodeIgniter\Model;

class ExamSessionModel extends Model
{
    protected $table = 'exam_sessions';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'user_id',
        'exam_id',
        'start_time',
        'end_time',
        'actual_submit_time',
        'status',
        'tab_switch_count',
        'terminated_reason',
        'total_questions_attempted',
        'correct_answers',
        'wrong_answers',
        'unanswered',
        'raw_score',
        'final_score',
        'percentage'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Get active session for user and exam
     */
    public function getActiveSession($userId, $examId)
    {
        return $this->where('user_id', $userId)
            ->where('exam_id', $examId)
            ->where('status', 'in_progress')
            ->first();
    }

    /**
     * Get session with exam details
     */
    public function getSessionWithExam($sessionId)
    {
        $session = $this->find($sessionId);
        if (!$session) {
            return null;
        }

        $examModel = new ExamModel();
        $session->exam = $examModel->getExamWithSubjects($session->exam_id);

        return $session;
    }

    /**
     * Check if session has expired
     */
    public function isSessionExpired($sessionId)
    {
        $session = $this->find($sessionId);
        if (!$session || $session->status !== 'in_progress') {
            return true;
        }

        $endTime = strtotime($session->end_time);
        return time() > $endTime;
    }

    /**
     * Get user's completed sessions
     */
    public function getUserCompletedSessions($userId)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('exam_sessions es');
        $builder->select('es.*, e.title as exam_title');
        $builder->join('exams e', 'e.id = es.exam_id');
        $builder->where('es.user_id', $userId);
        $builder->where('es.status', 'completed');
        $builder->orderBy('es.created_at', 'DESC');

        return $builder->get()->getResult();
    }
}
