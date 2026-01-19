<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Responder\Contract;

use Junco\Mvc\Result;
use Psr\Http\Message\ResponseInterface;

interface ResponderInterface
{
    /**
     * Creates a simplified response with a message.
     * 
     * @param Result|string $message
     * @param int $statusCode
     * @param int $code
     * 
     * @return ResponseInterface
     */
    public function responseWithMessage(Result|string $message = '', int $statusCode = 0, int $code = 0): ResponseInterface;

    /**
     * Create a response.
     * 
     * @param int $statusCode
     * @param string $reasonPhrase
     * 
     * @return ResponseInterface
     */
    public function response(int $statusCode = 200, string $reasonPhrase = ''): ResponseInterface;
}
