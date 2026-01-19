<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Dashboard;

interface DashboardInterface
{
    /**
     * Section
     * 
     * @param string $html
     * 
     * @return void
     */
    public function section(string $html): void;

    /**
     * Render
     * 
     * @return string
     */
    public function render(): string;
}
