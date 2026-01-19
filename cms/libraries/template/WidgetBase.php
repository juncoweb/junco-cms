<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Template;

abstract class WidgetBase implements WidgetInterface
{
    // vars
    protected array $rows = [];
    protected array $section = [
        'title'   => '',
        'content' => '',
        'css'     => ''
    ];

    /**
     * Section
     * 
     * @param array $section
     */
    public function section(array $section): static
    {
        $this->rows[] = array_merge($this->section, $section);

        return $this;
    }
}
