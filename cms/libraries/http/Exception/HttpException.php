<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Http\Exception;

use Exception;
use Throwable;

class HttpException extends Exception implements HttpThrowableInterface
{
    protected int $statusCode = 0;

    /**
     * Constructor
     */
    public function __construct(int $statusCode = 0, string $message = '', ?Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
        $this->statusCode = $statusCode ?: 422; // Default to 422 Unprocessable Entity
    }

    /**
     * Get
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
