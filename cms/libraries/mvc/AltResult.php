<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Mvc;

class AltResult
{
    /**
     * Constructor
     */
    public function __construct(
        private bool  $isOk,
        private mixed $value
    ) {}

    /**
     * Set
     */
    public function isOk(): bool
    {
        return $this->isOk;
    }

    /**
     * Get
     */
    public function getValue(): mixed
    {
        return $this->value;
    }
}
