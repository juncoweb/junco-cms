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
     * @param string $url
     * 
     * @return void
     */
    public function url(string $route = ''): void;

    /**
     * Render
     * 
     * @return void
     */
    public function render(): string;
}
