<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Backlist\Contract;

use Junco\Form\Contract\ActionElementsInterface;

interface ActionsInterface extends ActionElementsInterface
{
    /**
     * Create
     */
    public function create(array|int $attr = 0);

    /**
     * Edit
     */
    public function edit();

    /**
     * Delete
     */
    public function delete();

    /**
     * Back
     * 
     * @param string $href
     * @param string $title
     */
    public function back(string $href = '', string $title = '');

    /**
     * Toggle
     * 
     * @param array|string $control
     * @param string       $title
     * @param array|string $attr
     */
    public function toggle(string|array $control = '', string $title = '', array|string $attr = []);

    /**
     * Separate
     */
    public function separate(): void;

    /**
     * Filters
     */
    public function filters();

    /**
     * Refresh
     */
    public function refresh();
}
