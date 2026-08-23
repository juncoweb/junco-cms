<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

class FormCaptcha
{
    /**
     * Get
     */
    public static function get(string $captcha): array
    {
        $plugin = Plugin::get('captcha', 'load', $captcha);

        if ($plugin) {
            $attr = $plugin->run();

            if (is_array($attr)) {
                return $attr;
            }
        }

        return [];
    }

    /**
     * Verify
     */
    public static function verify(string $captcha): bool
    {
        if (!$captcha) {
            return true;
        }

        return (bool)Plugin::get('captcha', 'verify', $captcha)?->run();
    }
}
