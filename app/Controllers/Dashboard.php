<?php
namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\ExamModel;
use App\Models\ExamSessionModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $user = auth()->user();

        // Get available exams
        $examModel = new ExamModel();
        $availableExams = $examModel->getActiveExams();

        // Get user's previous attempts
        $sessionModel = new ExamSessionModel();
        $previousAttempts = $sessionModel->getUserCompletedSessions($user->id);

        $data = [
            'title' => 'Dashboard',
            'availableExams' => $availableExams,
            'previousAttempts' => $previousAttempts
        ];

        return view('dashboard/index', $data);
    }
}