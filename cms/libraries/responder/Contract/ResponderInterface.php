<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Responder\Contract;

use Psr\Http\Message\ResponseInterface;

interface ResponderInterface
{
    /**
     * Creates a simplified response with a message.
     * 
     * @param string $message
     * @param int    $code
     * 
     * @return ResponseInterface
     */
    public function message(string $message = '', int $code = 0): ResponseInterface;

    /**
     * Creates a simplified response with an alert message.
     * 
     * @param string $message
     * @param int    $code
     * 
     * return ResponseInterface
     */
    public function alert(string $message = '', int $code = 0): ResponseInterface;

    /**
     * Creates a response from the execution of an clousure function.
     * 
     * @return ResponseInterface
     */
    public function wrapper(callable $fn): ResponseInterface;

    /**
     * Create a response.
     * 
     * @return ResponseInterface
     */
    public function response(): ResponseInterface;
}
