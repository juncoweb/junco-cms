<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Responder\Contract\AjaxJsonInterface;
use Junco\Responder\ResponderBase;
use Psr\Http\Message\ResponseInterface;

class responder_master_default_ajax_json extends ResponderBase implements AjaxJsonInterface
{
    // vars
    protected array $content = [];

    /**
     * Sets the json content.
     * 
     * @param array $content
     * 
     * @return void
     */
    public function setContent(array $content): void
    {
        $this->content = $content;
    }

    /**
     * Creates a simplified response with a message.
     * 
     * @param string $message
     * @param int    $statusCode
     * @param int    $code
     * 
     * @return ResponseInterface
     */
    public function responseWithMessage(string $message = '', int $statusCode = 0, int $code = 0): ResponseInterface
    {
        $this->content = [
            '__message' => $message,
            '__code' => $code
        ];

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
            $this->content['error'] ??= $buffer;
        }

        return $this->createJsonResponse($this->content, $statusCode, $reasonPhrase);
    }
}
