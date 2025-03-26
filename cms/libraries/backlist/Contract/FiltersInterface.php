<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Backlist\Contract;

use Junco\Form\Contract\FilterElementsInterface;

interface FiltersInterface extends FilterElementsInterface
{
    /**
     * Sort
     * 
     * @param string $sort
     * @param int    $order
     * 
     * @return void
     */
    public function sort(string $sort = '', int $order = 0): void;

    /**
     * Adds filter controls to a table header.
     * 
     * @param string $header
     * 
     * @return string
     */
    public function sort_h(string $header = ''): string;

    /**
     * Render
     */
    public function render(): string;
}
