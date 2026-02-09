<?php

if (!function_exists('lang_text')) {
    /**
     * Get text in the current language with fallback to English
     * RENAMED to avoid conflict with PHP's built-in gettext() function
     *
     * @param string|null $englishText The English text
     * @param string|null $marathiText The Marathi text
     * @param string|null $language Optional language override (default: session language)
     * @return string The text in the selected language
     */
    function lang_text(?string $englishText, ?string $marathiText, ?string $language = null): string
    {
        // Get language from parameter or session (default to 'english')
        $lang = $language ?? session()->get('exam_language') ?? 'english';

        // Return Marathi if available and language is Marathi, otherwise fall back to English
        if ($lang === 'marathi' && !empty($marathiText)) {
            return $marathiText;
        }

        return $englishText ?? '';
    }
}

if (!function_exists('getCurrentLanguage')) {
    /**
     * Get the current exam language from session
     *
     * @return string 'english' or 'marathi'
     */
    function getCurrentLanguage(): string
    {
        return session()->get('exam_language') ?? 'english';
    }
}

if (!function_exists('setLanguage')) {
    /**
     * Set the exam language in session
     *
     * @param string $language 'english' or 'marathi'
     * @return void
     */
    function setLanguage(string $language): void
    {
        if (in_array($language, ['english', 'marathi'])) {
            session()->set('exam_language', $language);
        }
    }
}

if (!function_exists('toggleLanguage')) {
    /**
     * Toggle between English and Marathi
     *
     * @return string The new language
     */
    function toggleLanguage(): string
    {
        $currentLang = getCurrentLanguage();
        $newLang = ($currentLang === 'english') ? 'marathi' : 'english';
        setLanguage($newLang);
        return $newLang;
    }
}

if (!function_exists('getLanguageLabel')) {
    /**
     * Get display label for language
     *
     * @param string|null $language Optional language (default: current language)
     * @return string 'EN' or 'मर'
     */
    function getLanguageLabel(?string $language = null): string
    {
        $lang = $language ?? getCurrentLanguage();
        return ($lang === 'marathi') ? 'मर' : 'EN';
    }
}

if (!function_exists('__')) {
    /**
     * Get translated text for UI elements
     *
     * @param string $key Translation key (e.g., 'nav.dashboard')
     * @param array $params Optional parameters for sprintf replacement
     * @return string Translated text
     */
    function __(string $key, array $params = []): string
    {
        $language = getCurrentLanguage();

        // Load language file
        $langFile = APPPATH . "Language/{$language}/UI.php";

        if (!file_exists($langFile)) {
            // Fallback to English
            $langFile = APPPATH . "Language/english/UI.php";
        }

        $translations = require $langFile;

        // Get translation
        $text = $translations[$key] ?? $key;

        // Replace parameters if provided
        if (!empty($params)) {
            $text = vsprintf($text, $params);
        }

        return $text;
    }
}

if (!function_exists('getUILanguage')) {
    /**
     * Get the current UI language (not just exam language)
     * Checks session, then defaults to English
     *
     * @return string 'english' or 'marathi'
     */
    function getUILanguage(): string
    {
        return session()->get('ui_language') ?? session()->get('exam_language') ?? 'english';
    }
}

if (!function_exists('setUILanguage')) {
    /**
     * Set the UI language in session
     *
     * @param string $language 'english' or 'marathi'
     * @return void
     */
    function setUILanguage(string $language): void
    {
        if (in_array($language, ['english', 'marathi'])) {
            session()->set('ui_language', $language);
            session()->set('exam_language', $language); // Sync both
        }
    }
}
