<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Filesystem;

class FileHelper
{
    /**
     * Converts the size of a file into a text format.
     *
     * @param int $size The file size.
     */
    public static function toTextSize(int $size): string
    {
        if ($size == 0) {
            return '0';
        }

        if ($size == 1) {
            return '1 byte';
        }

        $units = ['bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

        for ($i = 0; $size >= 1024 && $i < 8; $i++) {
            $size /= 1024;
        }

        return sprintf(((int)$size == $size ? '%d' : '%.1f'), $size) . ' ' . $units[$i];
    }

    /**
     * Converts the size of a file from a text format to an integer number of bytes.
     *
     * @param string $size The file size in text format.
     */
    public static function toIntSize(string $size): int
    {
        if (!$size) {
            return 0;
        }

        $units = ['K', 'M', 'G', 'T', 'P', 'E', 'Z', 'Y', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

        if (preg_match('/^(.*)?\s?(K|M|G|T|P|E|Z|Y)(?:B)?$/', $size, $match)) {
            if (is_numeric($match[1])) {
                $exponent = array_search($match[2], $units) + 1;

                return (int)($match[1] * pow(1024, $exponent));
            }
        }

        return (int)$size;
    }

    /**
     * Convert the permissions of a file from a string to octal.
     * 
     * @param string $perm
     * 
     * @return string
     */
    public static function toOctalMode(string $perm): string
    {
        $trans = [
            '-' => '0',
            'r' => '4',
            'w' => '2',
            'x' => '1'
        ];
        $perm = substr(strtr($perm, $trans), 1);
        $perm_arr = str_split($perm, 3);

        return array_sum(str_split($perm_arr[0])) . array_sum(str_split($perm_arr[1])) . array_sum(str_split($perm_arr[2]));
    }
}
