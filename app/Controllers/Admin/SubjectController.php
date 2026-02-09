<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SubjectModel;

class SubjectController extends BaseController
{
    protected $subjectModel;

    public function __construct()
    {
        $this->subjectModel = new SubjectModel();
        helper('text'); // Load text helper
    }

    /**
     * Display list of all subjects
     */
    public function index()
    {
        // Check permission
        if (!auth()->user()->can('subjects.manage')) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $subjects = $this->subjectModel->orderBy('name', 'ASC')->findAll();

        $data = [
            'title' => 'Manage Subjects',
            'subjects' => $subjects
        ];

        return view('admin/subjects/index', $data);
    }

    /**
     * Show create subject form
     */
    public function create()
    {
        // Check permission
        if (!auth()->user()->can('subjects.manage')) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $data = [
            'title' => 'Create New Subject'
        ];

        return view('admin/subjects/create', $data);
    }

    /**
     * Store new subject
     */
    public function store()
    {
        // Check permission
        if (!auth()->user()->can('subjects.manage')) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $rules = [
            'name' => 'required|min_length[3]|max_length[100]|is_unique[subjects.name]',
            'code' => 'required|alpha_numeric|max_length[20]|is_unique[subjects.code]',
            'description' => 'permit_empty|max_length[500]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'code' => strtoupper($this->request->getPost('code')),
            'description' => $this->request->getPost('description')
        ];

        if ($this->subjectModel->insert($data)) {
            return redirect()->to('/admin/subjects')->with('success', 'Subject created successfully');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to create subject');
        }
    }

    /**
     * Show edit subject form
     */
    public function edit($id)
    {
        // Check permission
        if (!auth()->user()->can('subjects.manage')) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $subject = $this->subjectModel->find($id);

        if (!$subject) {
            return redirect()->to('/admin/subjects')->with('error', 'Subject not found');
        }

        $data = [
            'title' => 'Edit Subject',
            'subject' => $subject
        ];

        return view('admin/subjects/edit', $data);
    }

    /**
     * Update subject
     */
    public function update($id)
    {
        // Check permission
        if (!auth()->user()->can('subjects.manage')) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $subject = $this->subjectModel->find($id);

        if (!$subject) {
            return redirect()->to('/admin/subjects')->with('error', 'Subject not found');
        }

        $rules = [
            'name' => "required|min_length[3]|max_length[100]|is_unique[subjects.name,id,{$id}]",
            'code' => "required|alpha_numeric|max_length[20]|is_unique[subjects.code,id,{$id}]",
            'description' => 'permit_empty|max_length[500]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'code' => strtoupper($this->request->getPost('code')),
            'description' => $this->request->getPost('description')
        ];

        if ($this->subjectModel->update($id, $data)) {
            return redirect()->to('/admin/subjects')->with('success', 'Subject updated successfully');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to update subject');
        }
    }

    /**
     * Delete subject
     */
    public function delete($id)
    {
        // Check permission
        if (!auth()->user()->can('subjects.manage')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $subject = $this->subjectModel->find($id);

        if (!$subject) {
            return $this->response->setJSON(['success' => false, 'message' => 'Subject not found']);
        }

        // Check if subject has questions
        $db = \Config\Database::connect();
        $questionCount = $db->table('questions')->where('subject_id', $id)->countAllResults();

        if ($questionCount > 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => "Cannot delete subject. It has {$questionCount} question(s) associated with it."
            ]);
        }

        if ($this->subjectModel->delete($id)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Subject deleted successfully']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete subject']);
        }
    }
}
