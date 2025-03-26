<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

class template_install_default_snippet extends Template
{
    // vars
    protected $steps     = null;
    protected $submit    = false;


    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        // vars
        $this->css('assets/system.min.css');
        $this->js(['assets/system.min.js', 'app/install/js/install.js']);

        //
        $this->view = __DIR__ . '/view.html.php';;
        $this->steps = [
            ['task' => 'index', 'title' => _t('Language')],
            ['task' => 'license', 'title' => _t('License')],
            ['task' => 'requirements', 'title' => _t('Requirements')],
            ['task' => 'database', 'title' => _t('Database')],
            ['task' => 'extensions', 'title' => _t('Extensiones')],
            ['task' => 'settings', 'title' => _t('Settings')],
            ['task' => 'finish', 'title' => _t('Finish')],
        ];
    }

    /**
     * Navbar
     */
    protected function navbar()
    {
        $html = '';

        if ($this->options->hash != $this->steps[0]['task']) {
            $html .= '<button control-install="back" class="btn btn-primary btn-solid" title="' . ($t = _t('Back')) . '"><span aria-label="' . $t . '">&#60;</span></button>';
        } else {
            $html .= '<button class="btn" title="' . ($t = _t('Back')) . '" aria-disabled="true"><span aria-label="' . $t . '">&#60;</span></button>';
        }

        $html .= '<button control-install="refresh" class="btn btn-primary btn-solid" title="' . ($t = _t('Refresh')) . '"><span aria-label="' . $t . '">&orarr;</span></button>';

        if ($this->options->hash != $this->steps[count($this->steps) - 1]['task']) {
            $html .= '<button control-install="' . (empty($this->options->submit) ? 'next' : 'submit') . '" class="btn btn-primary btn-solid" title="' . ($t = _t('Next')) . '"><span aria-label="' . $t . '">&#62;</span></button>';
        } else {
            $html .= '<button class="btn" title="' . ($t = _t('Next')) . '" aria-disabled="true"><span aria-label="' . $t . '">&#62;</span></button>';
        }

        return '<nav class="btn-group btn-large">' . $html . '</nav>';
    }

    /**
     * Wizart
     */
    protected function wizard()
    {
        $html        = '';
        $css        = ' class="done"';
        $disabled    = '';

        foreach ($this->steps as $row) {
            $html .= '<li' . $css . ' data-step="' . $row['task'] . '" role="tab" ' . $disabled . '><span>' . $row['title'] . '</span></li>';
            if ($this->options->hash == $row['task']) {
                $css = '';
                $disabled = ' aria-disabled="true"';
            }
        }

        return '<ul class="wizard noselect" tabindex="0" aria-label="' . _t('List of steps') . '" role="tablist">' . $html . '</ul><!-- end wizard -->';
    }
}
