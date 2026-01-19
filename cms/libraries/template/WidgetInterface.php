<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Template;

interface WidgetInterface
{
    /**
     * Section
     * 
     * @param array $section
     * 
     * @return static
     */
    public function section(array $section): static;

    /**
     * Render
     * 
     * @return string
     */
    public function render(): string;
}
