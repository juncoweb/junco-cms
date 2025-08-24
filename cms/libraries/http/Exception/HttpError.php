<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Http\Exception;

use Error;
use Throwable;

class HttpError extends Error implements HttpThrowableInterface
{
    protected int $statusCode = 0;

    /**
     * Constructor
     */
    public function __construct(int $statusCode = 0, string $message = '', ?Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);

        $debug = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $this->statusCode = $statusCode ?: 500;
        $this->file       = $debug[1]['file'];
        $this->line       = $debug[1]['line'];
    }

    /**
     * Get
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
