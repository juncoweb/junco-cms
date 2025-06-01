<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\System;

trait EnumExportTrait
{
    /**
     * Get
     */
    public static function getFields(): array
    {
        $fields = [];

        foreach (self::cases() as $case) {
            $fields[$case->value] = $case->title();
        }

        return $fields;
    }

    /**
     * Get
     */
    public static function getList(array $fields = [], bool $abbr = true): array
    {
        $list = [];
        $cases = $fields
            ? self::fromList($fields)
            : self::cases();

        foreach ($cases as $case) {
            $list[$case->name] = $case->title($abbr);
        }

        return $list;
    }

    /**
     * From
     */
    public static function fromList(array $fields = []): array
    {
        if (!$fields) {
            return self::cases();
        }

        foreach ($fields as $i => $value) {
            $case = self::tryFrom($value);
            if ($case !== null) {
                $fields[$i] = $case;
            }
        }

        return $fields;
    }
}
