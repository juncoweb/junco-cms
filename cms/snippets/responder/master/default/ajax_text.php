<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

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
     * @param string $message
     * @param int    $code
     * 
     * @return ResponseInterface
     */
    public function message(string $message = '', int $code = 0): ResponseInterface
    {
        if ($code) {
            $message = "$code - $message";
        }

        $this->content = $message;
        return $this->response();
    }

    /**
     * Creates a simplified response with an alert message.
     * 
     * @param string $message
     * @param int    $code
     * 
     * return ResponseInterface
     */
    public function alert(string $message = '', $code = 0): ResponseInterface
    {
        $this->content = "$code - $message";
        return $this->response();
    }

    /**
     * Create a response.
     * 
     * @return ResponseInterface
     */
    public function response(): ResponseInterface
    {
        // ob
        $buffer = ob_get_contents();
        if ($buffer) {
            ob_end_clean();
            $this->content .= $buffer;
        }

        return $this->createTextResponse($this->content);
    }
}
