<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Backlist\Contract;

interface ColumnBaseInterface
{
    /**
     * Head
     * 
     * @return string
     */
    public function th(): string;

    /**
     * Body
     * 
     * @return string
     */
    public function td(): string;
}
