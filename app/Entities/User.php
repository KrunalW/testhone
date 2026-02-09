<?php

namespace App\Entities;

use CodeIgniter\Shield\Entities\User as ShieldUser;

/**
 * Extended User Entity with additional profile fields
 *
 * @property string|null $full_name User's full name
 * @property int|null    $age User's age
 * @property string|null $mobile_number User's mobile number
 * @property string|null $category User's category (sc/st, open, obc, etc.)
 * @property string|null $email User's email address
 * @property string      $preferred_language User's preferred language (english/marathi)
 */
class User extends ShieldUser
{
    /**
     * Additional fields for user profile
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id'                 => '?integer',
        'active'             => 'int-bool',
        'permissions'        => 'array',
        'groups'             => 'array',
        'age'                => '?integer',
        'full_name'          => '?string',
        'mobile_number'      => '?string',
        'category'           => '?string',
        'email'              => '?string',
        'preferred_language' => 'string',
    ];

    /**
     * Fillable fields for mass assignment
     *
     * @var list<string>
     */
    protected $datamap = [
        'full_name'          => 'full_name',
        'age'                => 'age',
        'mobile_number'      => 'mobile_number',
        'category'           => 'category',
        'email'              => 'email',
        'preferred_language' => 'preferred_language',
    ];

    /**
     * Get formatted category display name
     *
     * @return string
     */
    public function getCategoryDisplay(): string
    {
        $categories = [
            'sc/st' => 'SC/ST',
            'open'  => 'Open',
            'obc'   => 'OBC',
            'vj/nt' => 'VJ/NT',
            'nt-b'  => 'NT-B',
            'nt-c'  => 'NT-C',
            'nt-d'  => 'NT-D',
            'sebc'  => 'SEBC',
            'ews'   => 'EWS',
        ];

        return $categories[$this->category] ?? $this->category ?? 'N/A';
    }

    /**
     * Get formatted language display name
     *
     * @return string
     */
    public function getLanguageDisplay(): string
    {
        return $this->preferred_language === 'marathi' ? 'Marathi (मराठी)' : 'English';
    }

    /**
     * Get formatted mobile number with country code
     *
     * @return string
     */
    public function getFormattedMobile(): string
    {
        if (empty($this->mobile_number)) {
            return 'N/A';
        }

        return '+91 ' . $this->mobile_number;
    }
}
