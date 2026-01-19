<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Logger\Enum;

enum LogLevel: int
{
    case emergency = 1;
    case alert     = 2;
    case critical  = 3;
    case error     = 4;
    case warning   = 5;
    case notice    = 6;
    case info      = 7;
    case debug     = 8;

    /**
     * Title
     */
    public function title(): string
    {
        return match ($this) {
            self::emergency => 'EMERGENCY',
            self::alert     => 'ALERT',
            self::critical  => 'CRITICAL',
            self::error     => 'ERROR',
            self::warning   => 'WARNING',
            self::notice    => 'NOTICE',
            self::info      => 'INFO',
            self::debug     => 'DEBUG',
        };
    }

    /**
     * Get
     */
    public static function get(string $name): self
    {
        return is_numeric($name)
            ? self::from($name)
            : self::{$name};
    }

    /**
     * GetList
     */
    public static function getList(array $list = []): array
    {
        foreach (self::cases() as $case) {
            $list[$case->name] = $case->title();
        }

        return $list;
    }
}
