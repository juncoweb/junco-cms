<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

class template_frontend_empty_snippet extends Template
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->view = __DIR__ . '/view.html.php';
    }
}
