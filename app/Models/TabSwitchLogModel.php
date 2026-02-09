<?php

namespace App\Models;

use CodeIgniter\Model;

class TabSwitchLogModel extends Model
{
    protected $table = 'tab_switch_logs';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'session_id',
        'switched_at',
        'user_agent',
        'ip_address',
        'created_at'
    ];
    protected $useTimestamps = false;

    /**
     * Log tab switch
     */
    public function logSwitch($sessionId)
    {
        $request = \Config\Services::request();

        $data = [
            'session_id' => $sessionId,
            'switched_at' => date('Y-m-d H:i:s'),
            'user_agent' => $request->getUserAgent()->getAgentString(),
            'ip_address' => $request->getIPAddress(),
            'created_at' => date('Y-m-d H:i:s')
        ];

        return $this->insert($data);
    }

    /**
     * Get switch count for session
     */
    public function getSwitchCount($sessionId)
    {
        return $this->where('session_id', $sessionId)->countAllResults();
    }
}
