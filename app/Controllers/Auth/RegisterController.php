<?php

namespace App\Controllers\Auth;

use CodeIgniter\Shield\Controllers\RegisterController as ShieldRegister;
use CodeIgniter\HTTP\RedirectResponse;

class RegisterController extends ShieldRegister
{
    /**
     * Displays the registration form.
     */
    public function registerView()
    {
        if (auth()->loggedIn()) {
            return redirect()->to(config('Auth')->registerRedirect());
        }

        // Check if registration is allowed
        if (!setting('Auth.allowRegistration')) {
            return redirect()->back()->withInput()
                ->with('error', lang('Auth.registerDisabled'));
        }

        return $this->view(setting('Auth.views')['register']);
    }

    /**
     * Attempts to register the user with additional profile fields.
     */
    public function registerAction(): RedirectResponse
    {
        if (auth()->loggedIn()) {
            return redirect()->to(config('Auth')->registerRedirect());
        }

        // Check if registration is allowed
        if (!setting('Auth.allowRegistration')) {
            return redirect()->back()->withInput()
                ->with('error', lang('Auth.registerDisabled'));
        }

        $users = $this->getUserProvider();

        // Validate here first
        $rules = $this->getValidationRules();

        if (!$this->validateData($this->request->getPost(), $rules, [], config('Auth')->DBGroup)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Collect all user data
        $userData = [
            'username'           => $this->request->getPost('username'),
            'full_name'          => $this->request->getPost('full_name'),
            'age'                => $this->request->getPost('age'),
            'mobile_number'      => $this->request->getPost('mobile_number'),
            'category'           => $this->request->getPost('category'),
            'email'              => $this->request->getPost('email'),
            'preferred_language' => $this->request->getPost('preferred_language'),
            'active'             => 1, // Set active by default
        ];

        // Save user
        if (!$users->save($userData)) {
            return redirect()->back()->withInput()->with('errors', $users->errors());
        }

        $user = $users->findById($users->getInsertID());

        if ($user === null) {
            return redirect()->back()->withInput()->with('error', 'Failed to create user account.');
        }

        // Create email identity for login
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user->createEmailIdentity([
            'email'    => $email,
            'password' => $password,
        ]);

        // Add user to default group
        $defaultGroup = setting('AuthGroups.defaultGroup') ?? 'user';
        $user->addGroup($defaultGroup);

        // Auto-login the user
        auth()->login($user);

        // Set preferred language in session
        $preferredLanguage = $this->request->getPost('preferred_language');
        if (!empty($preferredLanguage)) {
            session()->set('exam_language', $preferredLanguage);
        }

        return redirect()->to(config('Auth')->registerRedirect())
            ->with('message', 'Registration successful! Welcome to the platform.');
    }

    /**
     * Returns the rules that should be used for validation.
     *
     * @return array<string, string>
     */
    protected function getValidationRules(): array
    {
        return [
            'username' => [
                'label' => 'Username',
                'rules' => 'required|max_length[30]|min_length[3]|regex_match[/\A[a-zA-Z0-9\.]+\z/]|is_unique[users.username]',
                'errors' => [
                    'required' => 'Username is required.',
                    'is_unique' => 'This username is already taken. Please choose another.',
                ]
            ],
            'full_name' => [
                'label' => 'Full Name',
                'rules' => 'required|max_length[100]|min_length[3]',
                'errors' => [
                    'required' => 'Full name is required.',
                    'min_length' => 'Full name must be at least 3 characters.',
                ]
            ],
            'age' => [
                'label' => 'Age',
                'rules' => 'required|integer|greater_than[0]|less_than[151]',
                'errors' => [
                    'required' => 'Age is required.',
                    'greater_than' => 'Please enter a valid age.',
                    'less_than' => 'Please enter a valid age.',
                ]
            ],
            'mobile_number' => [
                'label' => 'Mobile Number',
                'rules' => 'required|exact_length[10]|numeric|is_unique[users.mobile_number]',
                'errors' => [
                    'required' => 'Mobile number is required.',
                    'exact_length' => 'Mobile number must be exactly 10 digits.',
                    'numeric' => 'Mobile number must contain only numbers.',
                    'is_unique' => 'This mobile number is already registered.',
                ]
            ],
            'category' => [
                'label' => 'Category',
                'rules' => 'required|in_list[sc/st,open,obc,vj/nt,nt-b,nt-c,nt-d,sebc,ews]',
                'errors' => [
                    'required' => 'Category is required.',
                    'in_list' => 'Please select a valid category.',
                ]
            ],
            'email' => [
                'label' => 'Email',
                'rules' => 'required|valid_email|max_length[255]|is_unique[users.email]',
                'errors' => [
                    'required' => 'Email is required.',
                    'valid_email' => 'Please enter a valid email address.',
                    'is_unique' => 'This email address is already registered.',
                ]
            ],
            'preferred_language' => [
                'label' => 'Preferred Language',
                'rules' => 'required|in_list[english,marathi]',
                'errors' => [
                    'required' => 'Preferred language is required.',
                ]
            ],
            'password' => [
                'label' => 'Password',
                'rules' => 'required|min_length[8]',
                'errors' => [
                    'required' => 'Password is required.',
                    'min_length' => 'Password must be at least 8 characters.',
                ]
            ],
            'password_confirm' => [
                'label' => 'Confirm Password',
                'rules' => 'required|matches[password]',
                'errors' => [
                    'required' => 'Password confirmation is required.',
                    'matches' => 'Password confirmation does not match.',
                ]
            ],
        ];
    }
}
