<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Utils;

class Aleatory
{
    /**
     * Generate a token.
     *
     * @param int $len
     * 
     * @return string
     */
    public static function token(int $len = 16): string
    {
        $bytes = ceil($len * 6 / 8);

        return substr(strtr(base64_encode(random_bytes($bytes)), '+/', '-_'), 0, $len);
    }

    /**
     * Generates a random number code in a given range for a given length.
     *
     * @param int $len
     * 
     * @return int
     */
    public static function ntoken(int $len = 6): string
    {
        $token = random_int(0, (10 ** $len) - 1);
        $zeros = $len - strlen($token);

        if ($zeros) {
            $token = str_repeat('0', $zeros) . $token;
        }

        return $token;
    }

    /**
     * Generate a uuid.
     * 
     * @see: https://stackoverflow.com/questions/2040240/php-function-to-generate-v4-uuid/15875555#15875555
     * 
     * @return string
     */
    public static function uuid(): string
    {
        $data = random_bytes(16);

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
