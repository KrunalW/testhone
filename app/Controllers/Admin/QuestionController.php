<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\QuestionModel;
use App\Models\OptionModel;
use App\Models\SubjectModel;

class QuestionController extends BaseController
{
    protected $questionModel;
    protected $optionModel;
    protected $subjectModel;

    public function __construct()
    {
        $this->questionModel = new QuestionModel();
        $this->optionModel = new OptionModel();
        $this->subjectModel = new SubjectModel();
        helper('text'); // Load text helper for character_limiter()
    }

    public function index()
    {
        if (!auth()->user()->can('questions.manage')) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $subjectId = $this->request->getGet('subject_id');

        $builder = $this->questionModel->select('questions.*, subjects.name as subject_name, subjects.code as subject_code')
            ->join('subjects', 'subjects.id = questions.subject_id', 'left')
            ->orderBy('questions.created_at', 'DESC');

        if ($subjectId) {
            $builder->where('questions.subject_id', $subjectId);
        }

        $questions = $builder->findAll();
        $subjects = $this->subjectModel->orderBy('name', 'ASC')->findAll();

        $data = [
            'title' => 'Manage Questions',
            'questions' => $questions,
            'subjects' => $subjects,
            'selectedSubject' => $subjectId
        ];

        return view('admin/questions/index', $data);
    }

    public function create()
    {
        if (!auth()->user()->can('questions.manage')) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $subjects = $this->subjectModel->orderBy('name', 'ASC')->findAll();

        $data = [
            'title' => 'Create Question',
            'subjects' => $subjects
        ];

        return view('admin/questions/create', $data);
    }

    public function store()
    {
        if (!auth()->user()->can('questions.manage')) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $rules = [
            'subject_id' => 'required|is_natural_no_zero',
            'question_text' => 'required|min_length[10]',
            'question_type' => 'required|in_list[text,image]',
            'correct_option' => 'required|in_list[1,2,3,4]',
            'option_1_text' => 'required_without[option_1_image]',
            'option_2_text' => 'required_without[option_2_image]',
            'option_3_text' => 'required_without[option_3_image]',
            'option_4_text' => 'required_without[option_4_image]',
            'question_image' => 'if_exist|uploaded[question_image]|max_size[question_image,2048]|ext_in[question_image,jpg,jpeg,png,gif]',
            'option_1_image' => 'if_exist|uploaded[option_1_image]|max_size[option_1_image,1024]|ext_in[option_1_image,jpg,jpeg,png,gif]',
            'option_2_image' => 'if_exist|uploaded[option_2_image]|max_size[option_2_image,1024]|ext_in[option_2_image,jpg,jpeg,png,gif]',
            'option_3_image' => 'if_exist|uploaded[option_3_image]|max_size[option_3_image,1024]|ext_in[option_3_image,jpg,jpeg,png,gif]',
            'option_4_image' => 'if_exist|uploaded[option_4_image]|max_size[option_4_image,1024]|ext_in[option_4_image,jpg,jpeg,png,gif]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Handle question image upload
            $questionImagePath = null;
            if ($this->request->getFile('question_image')->isValid()) {
                $questionImage = $this->request->getFile('question_image');
                $questionImageName = $questionImage->getRandomName();
                $questionImage->move(WRITEPATH . 'uploads/questions', $questionImageName);
                $questionImagePath = 'uploads/questions/' . $questionImageName;
            }

            // Insert question
            $questionData = [
                'subject_id' => $this->request->getPost('subject_id'),
                'question_text' => $this->request->getPost('question_text'),
                'question_text_marathi' => $this->request->getPost('question_text_marathi'),
                'question_type' => $this->request->getPost('question_type'),
                'question_image_path' => $questionImagePath,
                'explanation' => $this->request->getPost('explanation'),
                'explanation_marathi' => $this->request->getPost('explanation_marathi'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $questionId = $this->questionModel->insert($questionData);

            if (!$questionId) {
                throw new \Exception('Failed to create question');
            }

            // Insert options
            $correctOption = (int)$this->request->getPost('correct_option');

            for ($i = 1; $i <= 4; $i++) {
                $optionImagePath = null;
                $optionImage = $this->request->getFile("option_{$i}_image");

                if ($optionImage && $optionImage->isValid()) {
                    $optionImageName = $optionImage->getRandomName();
                    $optionImage->move(WRITEPATH . 'uploads/options', $optionImageName);
                    $optionImagePath = 'uploads/options/' . $optionImageName;
                }

                $optionData = [
                    'question_id' => $questionId,
                    'option_text' => $this->request->getPost("option_{$i}_text"),
                    'option_text_marathi' => $this->request->getPost("option_{$i}_text_marathi"),
                    'option_image_path' => $optionImagePath,
                    'is_correct' => ($i === $correctOption) ? 1 : 0,
                    'display_order' => $i,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                $this->optionModel->insert($optionData);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }

            return redirect()->to('/admin/questions')->with('success', 'Question created successfully');

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Error creating question: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to create question: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        if (!auth()->user()->can('questions.manage')) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $question = $this->questionModel->find($id);

        if (!$question) {
            return redirect()->to('/admin/questions')->with('error', 'Question not found');
        }

        $options = $this->optionModel->where('question_id', $id)
            ->orderBy('display_order', 'ASC')
            ->findAll();

        $subjects = $this->subjectModel->orderBy('name', 'ASC')->findAll();

        $data = [
            'title' => 'Edit Question',
            'question' => $question,
            'options' => $options,
            'subjects' => $subjects
        ];

        return view('admin/questions/edit', $data);
    }

    public function update($id)
    {
        if (!auth()->user()->can('questions.manage')) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $question = $this->questionModel->find($id);

        if (!$question) {
            return redirect()->to('/admin/questions')->with('error', 'Question not found');
        }

        $rules = [
            'subject_id' => 'required|is_natural_no_zero',
            'question_text' => 'required|min_length[10]',
            'question_type' => 'required|in_list[text,image]',
            'correct_option' => 'required|in_list[1,2,3,4]',
            'question_image' => 'if_exist|uploaded[question_image]|max_size[question_image,2048]|ext_in[question_image,jpg,jpeg,png,gif]',
            'option_1_image' => 'if_exist|uploaded[option_1_image]|max_size[option_1_image,1024]|ext_in[option_1_image,jpg,jpeg,png,gif]',
            'option_2_image' => 'if_exist|uploaded[option_2_image]|max_size[option_2_image,1024]|ext_in[option_2_image,jpg,jpeg,png,gif]',
            'option_3_image' => 'if_exist|uploaded[option_3_image]|max_size[option_3_image,1024]|ext_in[option_3_image,jpg,jpeg,png,gif]',
            'option_4_image' => 'if_exist|uploaded[option_4_image]|max_size[option_4_image,1024]|ext_in[option_4_image,jpg,jpeg,png,gif]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Handle question image upload
            $questionImagePath = $question->question_image_path;
            if ($this->request->getFile('question_image')->isValid()) {
                // Delete old image
                if ($questionImagePath) {
                    $oldPath = WRITEPATH . str_replace('uploads/', 'uploads/', $questionImagePath);
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }

                $questionImage = $this->request->getFile('question_image');
                $questionImageName = $questionImage->getRandomName();
                $questionImage->move(WRITEPATH . 'uploads/questions', $questionImageName);
                $questionImagePath = 'uploads/questions/' . $questionImageName;
            }

            // Update question
            $questionData = [
                'subject_id' => $this->request->getPost('subject_id'),
                'question_text' => $this->request->getPost('question_text'),
                'question_text_marathi' => $this->request->getPost('question_text_marathi'),
                'question_type' => $this->request->getPost('question_type'),
                'question_image_path' => $questionImagePath,
                'explanation' => $this->request->getPost('explanation'),
                'explanation_marathi' => $this->request->getPost('explanation_marathi'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $this->questionModel->update($id, $questionData);

            // Update options
            $correctOption = (int)$this->request->getPost('correct_option');
            $existingOptions = $this->optionModel->where('question_id', $id)
                ->orderBy('display_order', 'ASC')
                ->findAll();

            for ($i = 1; $i <= 4; $i++) {
                $existingOption = $existingOptions[$i - 1] ?? null;
                $optionImagePath = $existingOption ? $existingOption->option_image_path : null;

                $optionImage = $this->request->getFile("option_{$i}_image");

                if ($optionImage && $optionImage->isValid()) {
                    // Delete old image
                    if ($optionImagePath) {
                        $oldPath = WRITEPATH . str_replace('uploads/', 'uploads/', $optionImagePath);
                        if (file_exists($oldPath)) {
                            unlink($oldPath);
                        }
                    }

                    $optionImageName = $optionImage->getRandomName();
                    $optionImage->move(WRITEPATH . 'uploads/options', $optionImageName);
                    $optionImagePath = 'uploads/options/' . $optionImageName;
                }

                $optionData = [
                    'option_text' => $this->request->getPost("option_{$i}_text"),
                    'option_text_marathi' => $this->request->getPost("option_{$i}_text_marathi"),
                    'option_image_path' => $optionImagePath,
                    'is_correct' => ($i === $correctOption) ? 1 : 0,
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                if ($existingOption) {
                    $this->optionModel->update($existingOption->id, $optionData);
                } else {
                    $optionData['question_id'] = $id;
                    $optionData['display_order'] = $i;
                    $optionData['created_at'] = date('Y-m-d H:i:s');
                    $this->optionModel->insert($optionData);
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }

            return redirect()->to('/admin/questions')->with('success', 'Question updated successfully');

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Error updating question: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to update question: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        if (!auth()->user()->can('questions.manage')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $question = $this->questionModel->find($id);

        if (!$question) {
            return $this->response->setJSON(['success' => false, 'message' => 'Question not found']);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Delete question image
            if ($question->question_image_path && file_exists(ROOTPATH . $question->question_image_path)) {
                unlink(ROOTPATH . $question->question_image_path);
            }

            // Get and delete option images
            $options = $this->optionModel->where('question_id', $id)->findAll();
            foreach ($options as $option) {
                if ($option->option_image_path && file_exists(ROOTPATH . $option->option_image_path)) {
                    unlink(ROOTPATH . $option->option_image_path);
                }
            }

            // Delete options (will cascade delete from user_answers due to foreign key)
            $this->optionModel->where('question_id', $id)->delete();

            // Delete question
            $this->questionModel->delete($id);

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }

            return $this->response->setJSON(['success' => true, 'message' => 'Question deleted successfully']);

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Error deleting question: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete question']);
        }
    }

    public function preview()
    {
        if (!auth()->user()->can('questions.manage')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $questionText = $this->request->getPost('question_text');
        $questionType = $this->request->getPost('question_type');
        $questionImage = $this->request->getPost('question_image_path'); // Existing image path

        // Build options array with text and image
        $options = [];
        for ($i = 1; $i <= 4; $i++) {
            $options[] = [
                'text' => $this->request->getPost("option_{$i}_text"),
                'image' => $this->request->getPost("option_{$i}_image_path") // Existing image path
            ];
        }

        $html = view('admin/questions/preview', [
            'question_text' => $questionText,
            'question_type' => $questionType,
            'question_image' => $questionImage,
            'options' => $options
        ]);

        return $this->response->setJSON(['success' => true, 'html' => $html]);
    }
}
