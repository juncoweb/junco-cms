<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;

class DashboardModel extends Model
{
    // vars
    protected array $config;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->config = config('dashboard');
    }

    /**
     * Get
     */
    public function getAdminIndexData()
    {
        return [
            'snippet' => $this->config['dashboard.admin_snippet'],
            'plugins' => $this->config['dashboard.admin_plugins'],
            'options' => $this->config['dashboard.admin_options']
        ];
    }

    /**
     * Get
     */
    public function getMyspaceIndexData()
    {
        return [
            'snippet' => $this->config['dashboard.myspace_snippet'],
            'plugins' => $this->config['dashboard.myspace_plugins'],
            'options' => $this->config['dashboard.myspace_options']
        ];
    }
}
