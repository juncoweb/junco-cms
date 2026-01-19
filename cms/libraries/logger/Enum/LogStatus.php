<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Logger\Enum;

enum LogStatus: int
{
    case unchecked = 0;
    case checked   = 1;
    case repeated  = 2;

    /**
     * Title
     */
    public function title(): string
    {
        return match ($this) {
            self::unchecked => _t('Unchecked'),
            self::checked   => _t('Checked'),
            self::repeated  => _t('Repeated'),
        };
    }

    /**
     * Color
     */
    public function color(): string
    {
        return match ($this) {
            self::unchecked => 'red',
            self::checked   => 'green',
            self::repeated  => 'subtle-default',
        };
    }

    /**
     * Fetch
     */
    public function fetch(): array
    {
        return [
            'title' => $this->title(),
            'color' => $this->color()
        ];
    }

    /**
     * Toggle
     */
    public function toggle(): self
    {
        return match ($this) {
            self::unchecked => self::checked,
            self::checked   => self::unchecked,
            self::repeated  => self::repeated,
        };
    }

    /**
     * Get
     */
    public static function get(int|string $name): self
    {
        return is_numeric($name)
            ? self::from($name)
            : self::{$name};
    }

    /**
     * Get
     */
    public static function getActives(): array
    {
        return [self::unchecked, self::checked];
    }

    /**
     * Get
     */
    public static function getList(array $list = []): array
    {
        foreach (self::cases() as $case) {
            $list[$case->name] = $case->title();
        }

        return $list;
    }
}
