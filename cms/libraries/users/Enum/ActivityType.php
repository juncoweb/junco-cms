<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Users\Enum;

enum ActivityType
{
    case signup;
    case activation;
    case login;
    case autologin;
    case savepwd;
    case savemail;
    case validation;

    /**
     * Title
     */
    public function title(): string
    {
        return match ($this) {
            self::signup     => _t('Signup'),
            self::activation => _t('Activation'),
            self::login      => _t('Login'),
            self::autologin  => _t('Autologin'),
            self::savepwd    => _t('Savepwd'),
            self::savemail   => _t('Savemail'),
            self::validation => _t('Validation'),
        };
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
    public static function getList(array $list = []): array
    {
        foreach (self::cases() as $case) {
            $list[$case->name] = $case->title();
        }

        return $list;
    }
}
