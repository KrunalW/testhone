<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Dashboard::index');
$routes->get('landing_page',function () {
    return view('landing_page');
});

// Custom registration routes (must come before Shield's auth routes)
$routes->get('register', 'Auth\RegisterController::registerView', ['as' => 'register']);
$routes->post('register', 'Auth\RegisterController::registerAction');

// Shield authentication routes (excluding register which we override above)
service('auth')->routes($routes, ['except' => ['register']]);

// Image serving route (for uploaded images in writable folder)
$routes->get('uploads/(:segment)/(:any)', 'ImageController::serve/$1/$2');

// Language Switcher (Global - works for all pages)
$routes->post('switch-language', 'LanguageController::switch');

// Dashboard & Profile
$routes->get('/dashboard', 'Dashboard::index', ['filter' => 'session']);
$routes->get('profile', 'Profile::index', ['filter' => 'session']);

// Exam Routes
$routes->group('exam', ['filter' => 'session'], function ($routes) {
    $routes->get('instructions/(:num)', 'ExamController::instructions/$1');
    $routes->post('start/(:num)', 'ExamController::start/$1');
    $routes->get('take/(:num)', 'ExamController::take/$1');
    $routes->post('save-answer', 'ExamController::saveAnswer');
    $routes->post('clear-answer', 'ExamController::clearAnswer');
    $routes->post('log-tab-switch', 'ExamController::logTabSwitch');
    $routes->post('submit', 'ExamController::submit');
    $routes->get('result/(:num)', 'ExamController::result/$1');
    $routes->get('feedback/(:num)', 'ExamController::feedback/$1');
    $routes->post('submit-feedback', 'ExamController::submitFeedback');
    $routes->post('get-remaining-time', 'ExamController::getRemainingTime');
    $routes->post('switch-language', 'ExamController::switchLanguage');
});

// Admin Routes
$routes->group('admin', ['filter' => 'session'], function ($routes) {
    // Subjects Management
    $routes->get('subjects', 'Admin\SubjectController::index');
    $routes->get('subjects/create', 'Admin\SubjectController::create');
    $routes->post('subjects/store', 'Admin\SubjectController::store');
    $routes->get('subjects/edit/(:num)', 'Admin\SubjectController::edit/$1');
    $routes->post('subjects/update/(:num)', 'Admin\SubjectController::update/$1');
    $routes->post('subjects/delete/(:num)', 'Admin\SubjectController::delete/$1');

    // Questions Management
    $routes->get('questions', 'Admin\QuestionController::index');
    $routes->get('questions/create', 'Admin\QuestionController::create');
    $routes->post('questions/store', 'Admin\QuestionController::store');
    $routes->get('questions/edit/(:num)', 'Admin\QuestionController::edit/$1');
    $routes->post('questions/update/(:num)', 'Admin\QuestionController::update/$1');
    $routes->post('questions/delete/(:num)', 'Admin\QuestionController::delete/$1');
    $routes->post('questions/preview', 'Admin\QuestionController::preview');

    // Exams Management
    $routes->get('exams', 'Admin\ExamAdminController::index');
    $routes->get('exams/create', 'Admin\ExamAdminController::create');
    $routes->post('exams/store', 'Admin\ExamAdminController::store');
    $routes->get('exams/edit/(:num)', 'Admin\ExamAdminController::edit/$1');
    $routes->post('exams/update/(:num)', 'Admin\ExamAdminController::update/$1');
    $routes->get('exams/schedule/(:num)', 'Admin\ExamAdminController::schedule/$1');
    $routes->post('exams/update-schedule/(:num)', 'Admin\ExamAdminController::updateSchedule/$1');
    $routes->post('exams/delete/(:num)', 'Admin\ExamAdminController::delete/$1');

    // Users Management
    $routes->get('users', 'Admin\UserController::index');
    $routes->get('users/create', 'Admin\UserController::create');
    $routes->post('users/store', 'Admin\UserController::store');
    $routes->post('users/delete/(:num)', 'Admin\UserController::delete/$1');
});
