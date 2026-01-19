<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Responder;

use Junco\Responder\Contract\ResponderInterface;
use Junco\Http\Message\HttpFactory;
use Psr\Http\Message\ResponseInterface;

abstract class ResponderBase implements ResponderInterface
{
    /**
     * Create a full-bodied response from a string.
     * 
     * @param string $content
     * @param int    $statusCode
     * @param string $reasonPhrase
     * 
     * @return ResponseInterface
     */
    protected function createTextResponse(string $content, int $statusCode = 0, string $reasonPhrase = ''): ResponseInterface
    {
        $factory = new HttpFactory;
        $stream = $factory->createStream($content);

        return $factory
            ->createResponse($statusCode, $reasonPhrase)
            ->withBody($stream);
    }

    /**
     * Create a full-bodied response from a array.
     * 
     * @param array  $content
     * @param int    $statusCode
     * @param string $reasonPhrase
     * 
     * @return ResponseInterface
     */
    protected function createJsonResponse(array $content, int $statusCode = 0, string $reasonPhrase = ''): ResponseInterface
    {
        $content = json_encode($content);
        $factory = new HttpFactory;
        $stream  = $factory->createStream($content);

        return $factory
            ->createResponse($statusCode, $reasonPhrase)
            ->withHeader('Content-Type', 'application/json; charset=utf-8')
            ->withBody($stream);
    }

    /**
     * Get output buffer
     * 
     * @return string
     */
    protected function getOutputBuffer(): string
    {
        $buffer = '';
        while (ob_get_level()) {
            $buffer = ob_get_clean() . $buffer;
        }

        return $buffer;
    }
}
