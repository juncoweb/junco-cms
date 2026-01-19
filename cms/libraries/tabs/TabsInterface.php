<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Tabs;

interface TabsInterface
{
    /**
     * Constructor
     * 
     * @param string|array $id
     * @param array        $options
     */
    public function __construct(string|array $id = '', array $options = []);

    /**
     * Tab
     * 
     * @param string $tab
     * @param string $tabpanel
     * 
     * @return void
     */
    public function tab(string $tab, string $tabpanel = ''): void;

    /**
     * Render
     */
    public function render(): string;
}
