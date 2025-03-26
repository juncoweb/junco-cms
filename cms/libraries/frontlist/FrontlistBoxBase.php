<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Frontlist;

use Junco\Frontlist\Contract\ActionsInterface;
use Junco\Frontlist\Contract\BoxInterface;

abstract class FrontlistBoxBase implements BoxInterface
{
    // vars
    protected string $actions_snippet        = '';
    protected ?ActionsInterface $actions    = null;

    /**
     * Constructor
     * 
     * @param string $id
     */
    public function __construct(protected string $id = '')
    {
        $this->id = $id;
    }

    /**
     * Actions
     * 
     * @param string $snippet
     * 
     * @return ActionsInterface
     */
    public function getActions(string $snippet = ''): ActionsInterface
    {
        return $this->actions = snippet('frontlist#actions', $snippet ?: $this->actions_snippet);
    }

    /**
     * Render
     * 
     * @param string $content
     * 
     * @return string
     */
    public function render(string $content = ''): string
    {
        return    "\n" . '<div id="' . ($this->id ? $this->id . '-' : '') . 'frontlist" class="frontlist-box">'
            .  (isset($this->actions) ? $this->actions->render() : '')
            .  '<div frontlist-slot aria-live="polite">' . $content . '</div>'
            . '</div>';
    }
}
