<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Backlist\Enum;

enum DefaultStatus: int
{
    case private = 0;
    case public  = 1;

    /**
     * Title
     */
    public function title(): string
    {
        return match ($this) {
            self::private => _t('Private'),
            self::public  => _t('Public'),
        };
    }

    /**
     * Color
     */
    public function color(): string
    {
        return match ($this) {
            self::private => 'red',
            self::public  => 'green',
        };
    }

    /**
     * Fetch
     */
    public function fetch(): array
    {
        return [
            //'name'  => $this->name,
            'title' => $this->title(),
            'color' => $this->color()
        ];
    }

    /**
     * Get
     */
    public static function fetchAll(): array
    {
        $list = [];
        foreach (self::cases() as $case) {
            $list[$case->value] = [
                'title' => $case->title(),
                'color' => $case->color()
            ];
        }

        return $list;
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
    public static function getList(): array
    {
        $list = [];
        foreach (self::cases() as $case) {
            $list[$case->name] = $case->title();
        }

        return $list;
    }

    /**
     * Toggle
     */
    public static function toggle(): string
    {
        $public  = self::public->value;
        $private = self::private->value;

        return "IF(status = $public, $private, $public)";
    }
}
