<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Debugger\ThrowableHandler;

class Debugger
{
    /**
     * Constructor
     */
    public function __construct()
    {
        // error handler
        if (SYSTEM_HANDLE_ERRORS) {
            error_reporting(0);
            ini_set('display_errors', 0);
            set_error_handler(function (
                int    $code,
                string $message,
                string $file = '',
                int    $line = 0,
                array  $context = []
            ) {
                $level   = $this->codeToLevel($code);
                $message = $this->codeToString($code) . ': ' . $message;
                $context = [
                    'code'      => $code,
                    'file'      => $file,
                    'line'      => $line,
                    'backtrace' => $this->getTraceAsString(debug_backtrace())
                ];

                app('logger')->log($level, $message, $context);
            });
        } else {
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
        }

        // exception handler
        set_exception_handler(function (Throwable $e) {
            (new ThrowableHandler)->emit($e);
        });
    }

    /**
     * Get
     * 
     * @param array $traces
     * 
     * @return string
     */
    protected function getTraceAsString(array $traces): string
    {
        foreach ($traces as $i => $trace) {
            if (isset($trace['args'])) {
                if (is_array($trace['args'])) {
                    $trace['args'] = $this->getArgsAsString($trace['args']);
                }
            } else {
                $trace['args'] = '';
            }
            if (isset($trace['class'])) {
                $trace['function'] = $trace['class'] . $trace['type'] . $trace['function'];
            }

            $traces[$i] = sprintf(
                '#%d %s(%d): %s(%s)',
                $i,
                $trace['file'] ?? '',
                $trace['line'] ?? 0,
                $trace['function'],
                $trace['args']
            );
        }

        return implode("\n", $traces);
    }

    /**
     * Get
     * 
     * @param array $args
     * 
     * @return string
     */
    protected function getArgsAsString(array $args): string
    {
        foreach ($args as $i => $value) {
            $type = gettype($value);
            switch ($type) {
                case 'integer':
                case 'double':
                    break;

                case 'object':
                    $args[$i] = get_class($value);
                    break;

                case 'string':
                    $args[$i] = "'" . $this->cutText($value) . "'";
                    break;

                case 'boolean':
                    $args[$i] = $value ? 'true' : 'false';
                    break;

                default:
                    $args[$i] = ucfirst($type);
                    break;
            }
        }

        return implode(', ', $args);
    }

    /**
     * Cut text
     * 
     * @param string $value
     * @param int    $max
     * 
     * @return string
     */
    protected function cutText(string $value, int $max = 30): string
    {
        return (strlen($value) > $max)
            ? substr($value, 0, $max) . '...'
            : $value;
    }

    /**
     * Code To String
     * 
     * @param int $code
     * 
     * @return string
     */
    protected function codeToString(int $code): string
    {
        switch ($code) {
            case E_ERROR:
                return 'E_ERROR';
            case E_WARNING:
                return 'E_WARNING';
            case E_PARSE:
                return 'E_PARSE';
            case E_NOTICE:
                return 'E_NOTICE';
            case E_CORE_ERROR:
                return 'E_CORE_ERROR';
            case E_CORE_WARNING:
                return 'E_CORE_WARNING';
            case E_COMPILE_ERROR:
                return 'E_COMPILE_ERROR';
            case E_COMPILE_WARNING:
                return 'E_COMPILE_WARNING';
            case E_DEPRECATED:
                return 'E_DEPRECATED';
            case E_USER_ERROR:
                return 'E_USER_ERROR';
            case E_USER_WARNING:
                return 'E_USER_WARNING';
            case E_USER_NOTICE:
                return 'E_USER_NOTICE';
            case E_USER_DEPRECATED:
                return 'E_USER_DEPRECATED';
            case E_STRICT:
                return 'E_STRICT';
            case E_RECOVERABLE_ERROR:
                return 'E_RECOVERABLE_ERROR';
        }

        return 'E_UNKNOW';
    }

    /**
     * Code To Level
     * 
     * @param int $code
     * 
     * @return string
     */
    protected function codeToLevel(int $code): string
    {
        switch ($code) {
            default:
            case E_ERROR:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
                return Logger::CRITICAL;

            case E_PARSE:
                return Logger::ALERT;

            case E_USER_ERROR:
            case E_RECOVERABLE_ERROR:
                return Logger::ERROR;

            case E_WARNING:
            case E_CORE_WARNING:
            case E_COMPILE_WARNING:
            case E_USER_WARNING:
                return Logger::WARNING;

            case E_NOTICE:
            case E_USER_NOTICE:
            case E_STRICT:
            case E_DEPRECATED:
            case E_USER_DEPRECATED:
                return Logger::NOTICE;
        }

        return Logger::DEBUG;
    }
}
