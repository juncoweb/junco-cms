<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

class FormDropdownOptions
{
    // vars
    private $options = [];

    /**
     * Set 
     *
     * @param array $option
     * 
     * @return void
     */
    public function push(array $option): void
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
