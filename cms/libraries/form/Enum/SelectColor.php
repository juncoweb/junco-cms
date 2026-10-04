<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Form\Enum;

enum SelectColor
{
    case blue;
    case indigo;
    case cyan;
    case green;
    case teal;
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
            self::indigo  => _t('Indigo'),
            self::cyan    => _t('Cyan'),
            self::green   => _t('Green'),
            self::teal    => _t('Teal'),
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
            self::indigo => 'indigo',
            self::cyan    => 'cyan',
            self::green   => 'green',
            self::teal    => 'teal',
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
}
