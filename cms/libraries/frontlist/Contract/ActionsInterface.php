<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Frontlist\Contract;

use Junco\Form\Contract\ActionElementsInterface;

interface ActionsInterface extends ActionElementsInterface
{
    /**
     * Create
     * 
     * @param array|string $attr
     */
    public function create(array|string $attr = []);

    /**
     * Back
     * 
     * @param string $href
     * @param string $title
     */
    public function back(string $href = '', string $title = '');

    /**
     * Refresh
     */
    public function refresh();

    /**
     * Separate
     */
    public function separate(): void;

    /**
     * Render
     */
    public function render(): string;
}
