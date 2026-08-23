<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Form\Enum;

enum SelectColor
{
    case blue;
    case skyblue;
    case cian;
    case green;
    case yellow;
    case orange;
    case red;
    case pink;
    case purple;

    /**
     * Title
     */
    public function title(): string
    {
        return match ($this) {
            self::blue    => _t('Blue'),
            self::skyblue => _t('Skyblue'),
            self::cian    => _t('Cian'),
            self::green   => _t('Green'),
            self::yellow  => _t('Yellow'),
            self::orange  => _t('Orange'),
            self::red     => _t('Red'),
            self::pink    => _t('Pink'),
            self::purple  => _t('Purple'),
        };
    }

    /**
     * Color
     */
    public function color(): string
    {
        return match ($this) {
            self::blue    => 'blue',
            self::skyblue => 'skyblue',
            self::cian    => 'cian',
            self::green   => 'green',
            self::yellow  => 'yellow',
            self::orange  => 'orange',
            self::red     => 'red',
            self::pink    => 'pink',
            self::purple  => 'purple',
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
     * Get
     */
    public static function get(string $name): self
    {
        return self::{$name};
    }

    /**
     * GetList
     */
    public static function getList(): array
    {
        $list = [];
        foreach (self::cases() as $case) {
            $list[$case->name] = $case->title();
        }

        return $list;
    }
}
