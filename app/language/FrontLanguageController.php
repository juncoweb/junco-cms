<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Controller;

class FrontLanguageController extends Controller
{
    /**
     * Index
     */
    public function index()
    {
        if (router()->isFormat('modal')) {
            return $this->view('IndexModal');
        }
        return $this->view();
    }

    /**
     * Content
     */
    protected function content()
    {
        return $this->view(null, (new FrontLanguageModel)->getContentData());
    }

    /**
     * Change
     */
    public function change()
    {
        return $this->wrapper(fn() => (new LanguageModel)->select());
        /* return $this->middleware('form.security')
			?: $this->wrapper(fn() => (new LanguageModel)->select()); */
    }
}
