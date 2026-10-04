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
     * @param string $label
     * @param string $panel
     * 
     * @return Tab
     */
    public function tab(string $label, string $panel = ''): Tab;

    /**
     * Render
     */
    public function render(): string;
}
