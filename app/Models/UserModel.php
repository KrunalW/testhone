<?php

namespace App\Models;

use CodeIgniter\Shield\Models\UserModel as ShieldUserModel;

class UserModel extends ShieldUserModel
{
    /**
     * Return type for user entity
     *
     * @var string
     */
    protected $returnType = \App\Entities\User::class;

    /**
     * Allowed fields for mass assignment
     * Includes additional profile fields
     *
     * @var list<string>
     */
    protected $allowedFields = [
        'username',
        'status',
        'status_message',
        'active',
        'last_active',
        'deleted_at',
        // Additional profile fields
        'full_name',
        'age',
        'mobile_number',
        'category',
        'email',
        'preferred_language',
    ];

    /**
     * Validation rules for user registration
     * Extends Shield's default rules with custom field validations
     *
     * @var array<string, string>
     */
    protected $validationRules = [
        'username' => [
            'label'  => 'Auth.username',
            'rules'  => 'required|max_length[30]|min_length[3]|regex_match[/\A[a-zA-Z0-9\.]+\z/]|is_unique[users.username]',
        ],
        'full_name' => [
            'label' => 'Full Name',
            'rules' => 'required|max_length[100]|min_length[3]',
        ],
        'age' => [
            'label' => 'Age',
            'rules' => 'required|integer|greater_than[0]|less_than[151]',
        ],
        'mobile_number' => [
            'label' => 'Mobile Number',
            'rules' => 'required|exact_length[10]|numeric|is_unique[users.mobile_number]',
        ],
        'category' => [
            'label' => 'Category',
            'rules' => 'required|in_list[sc/st,open,obc,vj/nt,nt-b,nt-c,nt-d,sebc,ews]',
        ],
        'email' => [
            'label' => 'Email',
            'rules' => 'required|valid_email|max_length[255]|is_unique[users.email]',
        ],
        'preferred_language' => [
            'label' => 'Preferred Language',
            'rules' => 'required|in_list[english,marathi]',
        ],
    ];

    /**
     * Validation messages
     *
     * @var array<string, array<string, string>>
     */
    protected $validationMessages = [
        'username' => [
            'is_unique' => 'This username is already taken. Please choose another.',
        ],
        'mobile_number' => [
            'exact_length' => 'Mobile number must be exactly 10 digits.',
            'numeric'      => 'Mobile number must contain only numbers.',
            'is_unique'    => 'This mobile number is already registered.',
        ],
        'email' => [
            'is_unique' => 'This email address is already registered.',
        ],
        'age' => [
            'greater_than' => 'Please enter a valid age.',
            'less_than'    => 'Please enter a valid age.',
        ],
        'category' => [
            'in_list' => 'Please select a valid category.',
        ],
        'preferred_language' => [
            'in_list' => 'Please select a valid language.',
        ],
    ];

    /**
     * Whether to trigger callbacks
     *
     * @var bool
     */
    protected $allowCallbacks = true;

    /**
     * Available callback types
     *
     * @var list<string>
     */
    protected $beforeInsert = ['beforeInsertUserData'];
    protected $beforeUpdate = ['beforeUpdateUserData'];

    /**
     * Callback to normalize data before insert
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    protected function beforeInsertUserData(array $data): array
    {
        // Set default preferred language if not provided
        if (!isset($data['data']['preferred_language'])) {
            $data['data']['preferred_language'] = 'english';
        }

        return $data;
    }

    /**
     * Callback to normalize data before update
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    protected function beforeUpdateUserData(array $data): array
    {
        return $data;
    }
}
