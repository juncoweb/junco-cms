<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Controller;

class AdminDashboardController extends Controller
{
    /**
     * Index
     */
    public function index()
    {
        return $this->view(null, (new DashboardModel)->getAdminIndexData());
    }
}
