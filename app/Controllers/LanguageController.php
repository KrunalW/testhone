<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;

class LanguageController extends BaseController
{
    /**
     * Switch UI and Exam language globally
     */
    public function switch()
    {
        if (!$this->request->isAJAX() && !$this->request->is('post')) {
            return redirect()->back();
        }

        $language = $this->request->getPost('language');

        if (in_array($language, ['english', 'marathi'])) {
            // Set both UI and exam language
            setUILanguage($language);
            setLanguage($language);

            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => true,
                    'language' => $language,
                    'message' => 'Language switched successfully'
                ]);
            }

            return redirect()->back()->with('success', 'Language switched to ' . ucfirst($language));
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid language'
            ]);
        }

        return redirect()->back()->with('error', 'Invalid language');
    }
}
