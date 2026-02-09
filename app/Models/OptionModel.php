<?php

namespace App\Models;

use CodeIgniter\Model;

class OptionModel extends Model
{
    protected $table = 'options';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'question_id',
        'option_text',
        'option_text_marathi',
        'option_image_path',
        'is_correct',
        'display_order'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'question_id' => 'required|is_natural_no_zero',
        'option_text' => 'required|max_length[500]',
        'is_correct' => 'required|in_list[0,1]',
        'display_order' => 'required|is_natural_no_zero'
    ];

    protected $validationMessages = [
        'question_id' => [
            'required' => 'Question ID is required',
            'is_natural_no_zero' => 'Invalid question ID'
        ],
        'option_text' => [
            'required' => 'Option text is required',
            'max_length' => 'Option text cannot exceed 500 characters'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Get all options for a question
     */
    public function getOptionsByQuestion($questionId)
    {
        return $this->where('question_id', $questionId)
                    ->orderBy('display_order', 'ASC')
                    ->findAll();
    }

    /**
     * Get correct option for a question
     */
    public function getCorrectOption($questionId)
    {
        return $this->where('question_id', $questionId)
                    ->where('is_correct', 1)
                    ->first();
    }

    /**
     * Delete all options for a question
     */
    public function deleteByQuestion($questionId)
    {
        return $this->where('question_id', $questionId)->delete();
    }
}
