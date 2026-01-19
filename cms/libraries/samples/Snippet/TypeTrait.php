<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Samples\Snippet;

trait TypeTrait
{
    const TYPE_INLINE = 1;
    const TYPE_FULL = 2;

    //
    protected int $type = 0;

    /**
     * Inline type
     * 
     * @return self
     */
    public function setInline(): self
    {
        $this->type = self::TYPE_INLINE;
        return $this;
    }

    /**
     * Full type
     * 
     * @return self
     */
    public function setFull(): self
    {
        $this->type = self::TYPE_FULL;
        return $this;
    }

    /**
     * Get
     */
    protected function getType(): string
    {
        return match ($this->type) {
            self::TYPE_INLINE => ' box-inline',
            self::TYPE_FULL => '',
            default => ' box-small'
        };
    }
}
