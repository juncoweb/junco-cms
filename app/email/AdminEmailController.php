<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Controller;

class AdminEmailController extends Controller
{
    /**
     * Index
     */
    public function index()
    {
        return $this->view();
    }

    /**
     * Write
     */
    public function write()
    {
        return $this->view();
    }

    /**
     * form
     */
    public function form()
    {
        return $this->view();
    }

    /**
     * Send
     */
    public function send()
    {
        return $this->middleware('form.security', 'extensions.security')
            ?: $this->wrapper(fn() => (new EmailModel)->send());
    }

    /**
     * Message
     */
    public function message()
    {
        return $this->view(null, (new AdminEmailModel)->getMessageData());
    }

    /**
     * Iframe
     */
    public function iframe()
    {
        return $this->view(null, (new AdminEmailModel)->getMessageData());
    }

    /**
     * Debug
     */
    public function debug()
    {
        return $this->view(null, (new AdminEmailModel)->getDebugData());
    }

    /**
     * Take
     */
    public function take()
    {
        return $this->middleware('form.security')
            ?: (new EmailDebugModel)->take();
    }
}
