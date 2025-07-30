<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Debugger;

use Junco\Http\Emitter\SapiEmitter;
use Junco\Http\Exception\HttpThrowableInterface;
use Error;
use System;
use Throwable;

class ThrowableHandler
{
    /**
     * Emit
     * 
     * @param Throwable $e
     * 
     * @return void
     */
    public function emit(Throwable $e): void
    {
        try {
            $response = $this->getResponse($e, true);
            (new SapiEmitter)->emit($response);
            die;
        } catch (Throwable $x) {
        }

        http_response_code(500);
        die(sprintf('%d - %s', $x->getCode(), $x->getTraceAsString()));
    }

    /**
     * Get
     * 
     * @param Throwable $e
     * 
     * @return 
     *  Psr\Http\Message\ResponseInterface
     *  Junco\Console\Output\OutputInterface
     */
    public function getResponse(Throwable $e, bool $severe = false)
    {
        $statusCode = $e instanceof HttpThrowableInterface
            ? $e->getStatusCode()
            : 0;

        $message = $e instanceof Error
            ? $this->handleThrowableError($e, $statusCode)
            : $e->getMessage();

        $code = $e->getCode();

        return System::getOutput($severe)->responseWithMessage($message, $statusCode, $code);
    }
    /**
     * Returns a message from a numeric code
     * 
     * @param int $code
     * 
     * @return string
     */
    public function getMessageFromCode(int $code = 0): string
    {
        switch ($code) {
            case 401:
                return sprintf(
                    _t('Please, you must %s or %s'),
                    '<a href="' . url('/usys/login', ['redirect' => -1]) . '">' . _t('Log in') . '</a>',
                    '<a href="' . url('/usys/signup') . '">' . _t('Sign Up') . '</a>'
                );
            case 403:
                return _t('Access denied.');
            case 404:
                return _t('The requested was not found on this server.');
            case 500:
                return sprintf(
                    _t('Fatal error in safety. Please help us to fix it by contacting the %sadministration%s.'),
                    '<a href="' . url('/contact') . '" target="_blank">',
                    '</a>'
                );
            default:
                return _t('A fatal error or a security failure has occurred.');
        }
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
