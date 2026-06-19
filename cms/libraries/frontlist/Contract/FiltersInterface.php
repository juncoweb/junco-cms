<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Frontlist\Contract;

use Junco\Form\Contract\FilterElementsInterface;

interface FiltersInterface extends FilterElementsInterface
{
    /**
     * Url
     * 
     * @param string $route
     * 
     * @return void
     */
    public function url(string $route = ''): void;

    /**
     * Render
     * 
     * @return string
     */
    public function render(): string;
}
