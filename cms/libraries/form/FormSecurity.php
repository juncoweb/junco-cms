<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Utils\Aleatory;

class FormSecurity
{
    /**
     * Verify
     */
    public static function getToken(bool $return_array = false)
    {
        $token_key = config('form.csrf_token_key');
        $session   = session();
        $token     = $session->get($token_key);

        if (!$token) {
            $token = Aleatory::token(12);
            $session->set($token_key, $token);
        }

        if ($return_array) {
            return ['name' => $token_key, 'value' => $token];
        }

        return '<input type="hidden" name="' . $token_key . '" value="' . $token . '"/>';
    }
}
