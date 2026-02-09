<?php

namespace App\Models;

use CodeIgniter\Model;

class ExamFeedbackModel extends Model
{
    protected $table            = 'exam_feedback';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'session_id',
        'user_id',
        'exam_id',
        'overall_experience_rating',
        'web_panel_experience',
        'question_quality',
        'will_refer_friends',
        'interested_next_test',
        'real_vs_mock_difference',
        'general_feedback',
        'felt_same_pressure',
        'other_test_series',
        'willing_to_pay',
        'amount_paid_range',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'session_id'                => 'required|integer',
        'user_id'                   => 'required|integer',
        'exam_id'                   => 'required|integer',
        'overall_experience_rating' => 'permit_empty|integer|greater_than[0]|less_than[11]',
        'web_panel_experience'      => 'permit_empty|in_list[poor,below_average,average,good,excellent]',
        'question_quality'          => 'permit_empty|in_list[poor,below_average,average,good,excellent]',
        'will_refer_friends'        => 'permit_empty|in_list[0,1]',
        'interested_next_test'      => 'permit_empty|in_list[0,1]',
        'felt_same_pressure'        => 'permit_empty|in_list[yes,no,maybe]',
        'willing_to_pay'            => 'permit_empty|in_list[0,1]',
        'amount_paid_range'         => 'permit_empty|integer|greater_than_equal_to[99]|less_than_equal_to[499]',
    ];

    protected $validationMessages = [
        'overall_experience_rating' => [
            'greater_than' => 'Rating must be between 1 and 10.',
            'less_than'    => 'Rating must be between 1 and 10.',
        ],
        'amount_paid_range' => [
            'greater_than_equal_to' => 'Amount must be between 99 and 499.',
            'less_than_equal_to'    => 'Amount must be between 99 and 499.',
        ],
    ];

    /**
     * Check if feedback exists for a session
     */
    public function hasFeedback(int $sessionId): bool
    {
        return $this->where('session_id', $sessionId)->countAllResults() > 0;
    }

    /**
     * Get feedback with related data
     */
    public function getFeedbackWithDetails(int $feedbackId)
    {
        return $this->db->table($this->table . ' f')
            ->select('f.*, u.username, u.full_name, e.title as exam_title, es.final_score, es.percentage')
            ->join('users u', 'u.id = f.user_id')
            ->join('exams e', 'e.id = f.exam_id')
            ->join('exam_sessions es', 'es.id = f.session_id')
            ->where('f.id', $feedbackId)
            ->get()
            ->getRow();
    }

    /**
     * Get all feedback for an exam
     */
    public function getExamFeedback(int $examId)
    {
        return $this->db->table($this->table . ' f')
            ->select('f.*, u.username, u.full_name, es.percentage, es.created_at as exam_date')
            ->join('users u', 'u.id = f.user_id')
            ->join('exam_sessions es', 'es.id = f.session_id')
            ->where('f.exam_id', $examId)
            ->orderBy('f.created_at', 'DESC')
            ->get()
            ->getResult();
    }

    /**
     * Get average ratings for an exam
     */
    public function getExamAverageRatings(int $examId)
    {
        return $this->db->table($this->table)
            ->select('
                COUNT(*) as total_feedback,
                AVG(overall_experience_rating) as avg_experience,
                SUM(CASE WHEN web_panel_experience = "excellent" THEN 1 ELSE 0 END) as panel_excellent,
                SUM(CASE WHEN web_panel_experience = "good" THEN 1 ELSE 0 END) as panel_good,
                SUM(CASE WHEN web_panel_experience = "average" THEN 1 ELSE 0 END) as panel_average,
                SUM(CASE WHEN question_quality = "excellent" THEN 1 ELSE 0 END) as quality_excellent,
                SUM(CASE WHEN question_quality = "good" THEN 1 ELSE 0 END) as quality_good,
                SUM(CASE WHEN will_refer_friends = 1 THEN 1 ELSE 0 END) as will_refer_count,
                SUM(CASE WHEN interested_next_test = 1 THEN 1 ELSE 0 END) as interested_next_count,
                SUM(CASE WHEN willing_to_pay = 1 THEN 1 ELSE 0 END) as willing_to_pay_count
            ')
            ->where('exam_id', $examId)
            ->get()
            ->getRow();
    }
}
