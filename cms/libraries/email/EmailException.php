<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

class EmailException extends Exception
{
    /**
     * Constructor
     */
    public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        if (SYSTEM_HANDLE_ERRORS) {
            app('logger')->error($message);
            $message = _t('Server failure');
        }

        parent::__construct($message, $code, $previous);
    }
}
