<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Dashboard;

abstract class DashboardBase implements DashboardInterface
{
    // vars
    protected array $sections = [];

    /**
     * Section
     * 
     * @param string $html
     * 
     * @return void
     */
    public function section(string $html): void
    {
        $this->sections[] = $html;
    }

    /**
     * Render
     * 
     * @return string
     */
    public function render(): string
    {
        return implode('', $this->sections);
    }
}
