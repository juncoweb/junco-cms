<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Frontlist\Contract;

interface BoxInterface
{
    /**
     * Actions
     * 
     * @param string $snippet
     * 
     * @return ActionsInterface
     */
    public function getActions(string $snippet = ''): ActionsInterface;

    /**
     * Render
     * 
     * @param string $content
     * 
     * @return string
     */
    public function render(string $content = ''): string;
}
