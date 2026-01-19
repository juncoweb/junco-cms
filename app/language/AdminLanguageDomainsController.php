<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Controller;

class AdminLanguageDomainsController extends Controller
{
    /**
     * Index
     */
    public function index()
    {
        return $this->view(null, (new AdminLanguageDomainsModel)->getIndexData());
    }

    /**
     * List
     */
    public function list(?array $data = null)
    {
        return $this->view(null, (new AdminLanguageDomainsModel)->setData($data)->getListData());
    }
}
