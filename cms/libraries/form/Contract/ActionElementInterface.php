<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Form\Contract;

interface ActionElementInterface
{
    /**
     * Get
     * 
     * @return string
     */
    public function render(): string;

    /**
     * To string representation.
     * 
     * @return string
     */
    public function __toString(): string;
}
