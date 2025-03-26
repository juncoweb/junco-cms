<?php

/**
 * Extensions exception
 */

namespace Junco\Extensions\XData;

class MalformedDataException extends \Exception
{
    public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null)
    {
        $debug        = debug_backtrace();
        array_shift($debug);
        $this->file    = $debug[0]['file'];
        $this->line    = $debug[0]['line'];

        if (!$message) {
            $class    = null;
            $path    = [];

            foreach ($debug as $row) {
                if ($class === null) {
                    $class = $row['class'];
                } elseif (isset($row['class']) && $class == $row['class']) {
                    $path[] = $row['function'];
                } else {
                    break;
                }
            }
            $path[] = $class;
            $message = implode(' -> ', array_reverse($path));
        }

        // logger
        app('logger')->alert($message, [
            'file' => $this->file,
            'line' => $this->line
        ]);

        if (SYSTEM_HANDLE_ERRORS) {
            $message = _t('The extensions manager has thrown an error.');
        }

        parent::__construct($message, $code, $previous);
    }
}
