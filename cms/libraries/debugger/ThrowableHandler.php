<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Debugger;

use Error;
use Throwable;

class ThrowableHandler
{
    /**
     * Returns a message from a numeric code
     * 
     * @param int $code
     * 
     * @return string
     */
    public function getMessageFromCode(int $code = 0): string
    {
        return match ($code) {
            401 => sprintf(
                _t('Please, you must %s or %s'),
                '<a href="' . url('/usys/login', ['redirect' => -1]) . '">' . _t('Log in') . '</a>',
                '<a href="' . url('/usys/signup') . '">' . _t('Sign up') . '</a>'
            ),
            403 => _t('Access denied.'),
            404 => _t('The requested was not found on this server.'),
            500 => sprintf(
                _t('Fatal error in safety. Please help us to fix it by contacting the %sadministration%s.'),
                '<a href="' . url('/contact') . '" target="_blank">',
                '</a>'
            ),
            default => _t('A fatal error or a security failure has occurred.'),
        };
    }

    /**
     * Handles captured errors
     * 
     * @param Error $e
     * 
     * @return string
     */
    protected function handleThrowableError(Error $e, int $statusCode = 0): string
    {
        if (SYSTEM_HANDLE_ERRORS) {
            try {
                app('logger')->alert(sprintf('%s: %s', get_class($e), $e->getMessage()), [
                    'code'      => $e->getCode(),
                    'file'      => $e->getFile(),
                    'line'      => $e->getLine(),
                    'backtrace' => $e->getTraceAsString()
                ]);

                return $this->getMessageFromCode($statusCode);
            } catch (Throwable $e) {
                return 'A fatal error or a security failure has occurred.';
            }
        }

        return str_replace("\n", '<br />', $e->__toString());
    }
}
