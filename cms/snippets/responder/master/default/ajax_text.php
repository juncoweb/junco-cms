<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Result;
use Junco\Responder\Contract\AjaxTextInterface;
use Junco\Responder\ResponderBase;
use Psr\Http\Message\ResponseInterface;

class responder_master_default_ajax_text extends ResponderBase implements AjaxTextInterface
{
    // vars
    protected string $content = '';

    /**
     * Sets the text content.
     * 
     * @param string $content
     * 
     * @return void
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    /**
     * Creates a simplified response with a message.
     * 
     * @param Result|string $message
     * @param int $statusCode
     * @param int $code
     * 
     * @return ResponseInterface
     */
    public function responseWithMessage(Result|string $message = '', int $statusCode = 0, int $code = 0): ResponseInterface
    {
        if ($message instanceof Result) {
            $statusCode = $message->getStatusCode();
            $code       = $message->getCode();
            $message    = $message->getMessage();
        }

        if ($code) {
            $message = "$code - $message";
        }

        $this->content = $message;
        return $this->response($statusCode);
    }

    /**
     * Create a response.
     * 
     * @param int $statusCode
     * @param string $reasonPhrase
     * 
     * @return ResponseInterface
     */
    public function response(int $statusCode = 200, string $reasonPhrase = ''): ResponseInterface
    {
        // ob
        $buffer = ob_get_contents();
        if ($buffer) {
            ob_end_clean();
            $this->content .= $buffer;
        }

        return $this->createTextResponse($this->content, $statusCode, $reasonPhrase);
    }
}
