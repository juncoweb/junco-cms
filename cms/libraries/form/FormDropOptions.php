<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

class FormDropOptions
{
    // vars
    private $options = [];

    /**
     * Set 
     *
     * @param string 
     */
    public function push($option)
    {
        $this->options[] = $option;
    }

    /**
     * Get
     */
    public function get(): array
    {
        return $this->options;
    }
}
