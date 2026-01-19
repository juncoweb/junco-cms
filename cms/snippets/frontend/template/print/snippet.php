<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

class template_frontend_print_snippet extends Template
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $config = config('frontend');

        $this->assets->options($config['frontend.default_options'] + [
            'theme' => $config['frontend.print_theme']
        ]);
        $this->alter_options = $config['frontend.alter_options'];
        $this->view = __DIR__ . '/view.html.php';
    }
}
