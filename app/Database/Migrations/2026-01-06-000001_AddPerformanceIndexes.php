<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPerformanceIndexes extends Migration
{
    public function up()
    {
        // Create indexes only if they don't exist
        $indexes = [
            'user_answers' => ['idx_user_answers_session_question', 'idx_user_answers_session'],
            'exam_sessions' => ['idx_exam_sessions_user_exam_status', 'idx_exam_sessions_status_endtime'],
            'questions' => ['idx_questions_exam_subject'],
            'options' => ['idx_options_question'],
            'exam_subject_distribution' => ['idx_exam_subject_dist_exam'],
            'exam_results' => ['idx_exam_results_session'],
            'tab_switch_logs' => ['idx_tab_switch_logs_session']
        ];

        // Composite index for user_answers: session_id + question_id (primary lookup pattern)
        $this->createIndexIfNotExists('user_answers', 'idx_user_answers_session_question',
            'CREATE INDEX idx_user_answers_session_question ON user_answers(session_id, question_id)');

        // Index for user_answers: session_id alone (for counting, stats)
        $this->createIndexIfNotExists('user_answers', 'idx_user_answers_session',
            'CREATE INDEX idx_user_answers_session ON user_answers(session_id)');

        // Index for exam_sessions: user_id + exam_id + status (for finding active sessions)
        $this->createIndexIfNotExists('exam_sessions', 'idx_exam_sessions_user_exam_status',
            'CREATE INDEX idx_exam_sessions_user_exam_status ON exam_sessions(user_id, exam_id, status)');

        // Index for exam_sessions: status + end_time (for expired session checks)
        $this->createIndexIfNotExists('exam_sessions', 'idx_exam_sessions_status_endtime',
            'CREATE INDEX idx_exam_sessions_status_endtime ON exam_sessions(status, end_time)');

        // Index for questions: subject_id (for fetching questions by subject)
        $this->createIndexIfNotExists('questions', 'idx_questions_subject',
            'CREATE INDEX idx_questions_subject ON questions(subject_id)');

        // Index for options: question_id (for fetching options for questions)
        $this->createIndexIfNotExists('options', 'idx_options_question',
            'CREATE INDEX idx_options_question ON options(question_id)');

        // Index for exam_subject_distribution: exam_id (for fetching subject distribution)
        $this->createIndexIfNotExists('exam_subject_distribution', 'idx_exam_subject_dist_exam',
            'CREATE INDEX idx_exam_subject_dist_exam ON exam_subject_distribution(exam_id)');

        // Index for exam_results: session_id (for fetching results)
        $this->createIndexIfNotExists('exam_results', 'idx_exam_results_session',
            'CREATE INDEX idx_exam_results_session ON exam_results(session_id)');

        // Index for tab_switch_logs: session_id (for counting switches)
        $this->createIndexIfNotExists('tab_switch_logs', 'idx_tab_switch_logs_session',
            'CREATE INDEX idx_tab_switch_logs_session ON tab_switch_logs(session_id)');
    }

    private function createIndexIfNotExists($table, $indexName, $createQuery)
    {
        $db = \Config\Database::connect();

        // Check if index exists
        $query = $db->query("SHOW INDEX FROM `{$table}` WHERE Key_name = '{$indexName}'");

        if ($query->getNumRows() == 0) {
            $db->query($createQuery);
        }
    }

    public function down()
    {
        // Drop all indexes
        $this->db->query('DROP INDEX IF EXISTS idx_user_answers_session_question ON user_answers');
        $this->db->query('DROP INDEX IF EXISTS idx_user_answers_session ON user_answers');
        $this->db->query('DROP INDEX IF EXISTS idx_exam_sessions_user_exam_status ON exam_sessions');
        $this->db->query('DROP INDEX IF EXISTS idx_exam_sessions_status_endtime ON exam_sessions');
        $this->db->query('DROP INDEX IF EXISTS idx_questions_subject ON questions');
        $this->db->query('DROP INDEX IF EXISTS idx_options_question ON options');
        $this->db->query('DROP INDEX IF EXISTS idx_exam_subject_dist_exam ON exam_subject_distribution');
        $this->db->query('DROP INDEX IF EXISTS idx_exam_results_session ON exam_results');
        $this->db->query('DROP INDEX IF EXISTS idx_tab_switch_logs_session ON tab_switch_logs');
    }
}
