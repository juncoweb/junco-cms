<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Samples\Snippet;

interface TypeInterface
{
    /**
     * Inline type
     * 
     * @return self
     */
    public function setInline(): self;

    /**
     * Full type
     * 
     * @return self
     */
    public function setFull(): self;
}
