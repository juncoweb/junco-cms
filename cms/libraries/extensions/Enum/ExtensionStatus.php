<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Extensions\Enum;

enum ExtensionStatus
{
    case prototype;
    case private;
    case public;
    case deprecated;

    /**
     * Title
     */
    public function title(): string
    {
        return match ($this) {
            self::prototype  => _t('Prototype'),
            self::private    => _t('Private'),
            self::public     => _t('Public'),
            self::deprecated => _t('Deprecated'),
        };
    }

    /**
     * Color
     */
    public function color(): string
    {
        return match ($this) {
            self::prototype  => 'yellow',
            self::private    => 'orange',
            self::public     => 'green',
            self::deprecated => 'red',
        };
    }

    /**
     * Get
     */
    public function isActive(): bool
    {
        return match ($this) {
            self::prototype  => false,
            self::private    => true,
            self::public     => true,
            self::deprecated => false,
        };
    }

    /**
     * Fetch
     */
    public function fetch(): array
    {
        return [
            'color' => $this->color(),
            'title' => $this->title(),
            'is_active' => $this->isActive(),
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
    public static function getList(array $list = []): array
    {
        foreach (self::cases() as $case) {
            $list[$case->name] = $case->title();
        }

        return $list;
    }

    /**
     * Get
     */
    public static function getActives(): array
    {
        return array_filter(self::cases(), fn($case) => $case->isActive());
    }

    /**
     * Is valid
     */
    public static function isValid(string $name): bool
    {
        return in_array($name, ['prototype', 'private', 'public', 'deprecated']);
    }
}
