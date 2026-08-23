<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Extensions\Enum;

enum UpdateStatus
{
    case cancelled;
    case available;
    case installed;

    /**
     * Title
     */
    public function title(): string
    {
        return match ($this) {
            self::cancelled  => _t('Cancelled'),
            self::available => _t('Available'),
            self::installed => _t('Installed'),
        };
    }

    /**
     * Color
     */
    public function color(): string
    {
        return match ($this) {
            self::cancelled  => 'red',
            self::available => 'orange',
            self::installed => 'green',
        };
    }

    /**
     * Fetch
     */
    public function fetch(): array
    {
        return [
            'color' => $this->color(),
            'title' => $this->title()
        ];
    }

    /**
     * Get
     */
    public static function get(string $name): self
    {
        return self::{$name};
    }

    /**
     * Get
     */
    public static function getActives(): array
    {
        return [self::cancelled, self::installed];
    }
}
