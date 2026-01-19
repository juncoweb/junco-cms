<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Controller;

class FrontSystemController extends Controller
{
    /**
     * 404
     */
    public function error404()
    {
        return $this->view();
    }

    /**
     * Index
     */
    public function index()
    {
        return $this->view();
    }
}
