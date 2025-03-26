<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

class dashboard_master_default_snippet
{
    // vars
    protected $rows = [];

    /**
     * 
     */
    public function row(string $html)
    {
        $this->rows[] = $html;
    }

    /**
     * 
     */
    public function render()
    {
        return implode('', $this->rows);
    }
}
