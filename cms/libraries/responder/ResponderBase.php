<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Responder;

use Junco\Responder\Contract\ResponderInterface;
use Junco\Http\Message\HttpFactory;
use Psr\Http\Message\ResponseInterface;

abstract class ResponderBase implements ResponderInterface
{
    /**
     * Creates a response from the execution of an clousure function.
     * 
     * @param callable $fn
     * 
     * @return ResponseInterface
     */
    public function wrapper(callable $fn): ResponseInterface
    {
        try {
            $code    = 1;
            $message = '';
            $data    = null;
            $result  = $fn();

            if ($result) {
                if (is_numeric($result)) {
                    $code = $result;
                } elseif (is_array($result)) {
                    $message = $result[0] ?? null;
                    $code    = $result[1] ?? 0;
                    $data    = $result[2] ?? null;
                } elseif ($result instanceof ResponseInterface) {
                    return $result;
                } else {
                    $message = $result;
                }
            }

            if ($message === '') {
                $message = _t('The task has been completed successfully.');
            } elseif (!$message) {
                $message = '';
            }

            return $this->message($message, $code, $data);
        } catch (\Exception $e) {
            return $this->message($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Create a full-bodied response from a string.
     * 
     * @param string &$content
     * 
     * @return ResponseInterface
     */
    protected function createTextResponse(string $content): ResponseInterface
    {
        $factory = new HttpFactory;
        $stream = $factory->createStream($content);

        return $factory
            ->createResponse()
            ->withBody($stream);
    }

    /**
     * Create a full-bodied response from a array.
     * 
     * @param array &$content
     * 
     * @return ResponseInterface
     */
    protected function createJsonResponse(array $content): ResponseInterface
    {
        $content = json_encode($content);
        $factory = new HttpFactory;
        $stream = $factory->createStream($content);

        return $factory
            ->createResponse()
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
