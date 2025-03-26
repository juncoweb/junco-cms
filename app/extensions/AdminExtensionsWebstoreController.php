<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Controller;

class AdminExtensionsWebstoreController extends Controller
{
    /**
     * Index
     */
    public function index()
    {
        return $this->view();
    }

    /**
     * List
     */
    public function list()
    {
        return $this->view(null, (new ExtensionsWebstoreModel)->getListData());
    }

    /**
     * Confirm download
     */
    public function confirmDownload()
    {
        return $this->view(null, (new ExtensionsWebstoreModel)->getConfirmDownloadData());
    }

    /**
     * Download
     */
    public function download()
    {
        return $this->middleware('form.security', 'extensions.security')
            ?: $this->wrapper(fn() => (new ExtensionsWebstoreModel)->download());
    }
}
