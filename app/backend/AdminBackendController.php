<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Controller;

class AdminBackendController extends Controller
{
    /**
     * Menus
     */
    public function menus()
    {
        return (new BackendModel)->getMenusData();
    }
}
