<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Users\Enum;

enum UserStatus
{
    case autosignup;
    case inactive;
    case active;

    /**
     * Title
     */
    public function title(): string
    {
        return match ($this) {
            self::autosignup => _t('Auto signup'),
            self::inactive   => _t('Inactive'),
            self::active     => _t('Active'),
        };
    }

    /**
     * Color
     */
    public function color(): string
    {
        return match ($this) {
            self::autosignup => 'orange',
            self::inactive   => 'red',
            self::active     => 'green',
        };
    }

    /**
     * Is
     */
    public function isPublic(): bool
    {
        return match ($this) {
            self::autosignup => false,
            self::inactive   => true,
            self::active     => true,
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
     * Is
     */
    public function isEqual(string $name): bool
    {
        return $this->name === $name;
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
    public static function getList(bool $public = false): array
    {
        if ($public) {
            return array_values(array_filter(self::cases(), fn($e) => $e->isPublic()));
        }

        return self::cases();
    }
}
