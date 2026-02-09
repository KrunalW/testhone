<?php

namespace App\Models;

use CodeIgniter\Model;

class ExamModel extends Model
{
    protected $table = 'exams';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'title',
        'title_marathi',
        'description',
        'description_marathi',
        'duration_minutes',
        'total_questions',
        'pass_percentage',
        'has_negative_marking',
        'negative_marks_per_question',
        'marks_per_question',
        'randomize_questions',
        'randomize_options',
        'prevent_tab_switch',
        'max_tab_switches_allowed',
        'status',
        'scheduled_start_time',
        'scheduled_end_time',
        'is_scheduled',
        'result_publish_time',
        'is_result_scheduled',
        'created_by'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Get exam with subject distribution
     */
    public function getExamWithSubjects($examId)
    {
        $exam = $this->find($examId);
        if (!$exam) {
            return null;
        }

        $db = \Config\Database::connect();
        $builder = $db->table('exam_subject_distribution esd');
        $builder->select('esd.*, s.name as subject_name, s.code as subject_code');
        $builder->join('subjects s', 's.id = esd.subject_id');
        $builder->where('esd.exam_id', $examId);
        $exam->subject_distribution = $builder->get()->getResult();

        return $exam;
    }

    /**
     * Get active exams (including scheduled exams)
     */
    public function getActiveExams()
    {
        return $this->whereIn('status', ['active', 'scheduled'])
            ->orderBy('scheduled_start_time', 'ASC')
            ->findAll();
    }

    /**
     * Check if exam is currently available for students
     */
    public function isExamAvailable($examId)
    {
        $exam = $this->find($examId);

        if (!$exam || !in_array($exam->status, ['active', 'scheduled'])) {
            return false;
        }

        // If not scheduled, it's available
        if (!$exam->is_scheduled) {
            return $exam->status === 'active';
        }

        // Check if within scheduled time window
        $now = new \DateTime('now', new \DateTimeZone('Asia/Kolkata'));
        $startTime = new \DateTime($exam->scheduled_start_time, new \DateTimeZone('Asia/Kolkata'));
        $endTime = new \DateTime($exam->scheduled_end_time, new \DateTimeZone('Asia/Kolkata'));

        return $now >= $startTime && $now <= $endTime && $exam->status === 'active';
    }
}
