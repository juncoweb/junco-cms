<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Result;
use Junco\Responder\ResponderBase;
use Psr\Http\Message\ResponseInterface;

class xjs_master_default_snippet extends ResponderBase
{
    // vars
    protected $json = [];

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
            $this->json = $message->render();
            return $this->response($message->getStatusCode());
        }

        if ($statusCode > 499) {
            return $this->alert($message, $statusCode, $code);
        }
        if ($message) {
            $this->json['message'] = $message;
        }
        if ($code) {
            $this->json['code'] = $code;
        }

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
        if (config('system.profiler')) {
            $this->json['__profiler'] = app('profiler')->render(true);
        }

        // error
        $buffer = $this->getOutputBuffer();

        if ($buffer) {
            if (SYSTEM_HANDLE_ERRORS) {
                trigger_error(strip_tags($buffer));
            } else {
                $this->json['__error'] = $buffer;
            }
        }

        return $this->createJsonResponse($this->json, $statusCode, $reasonPhrase);
    }

    /**
     * Creates a simplified response with an alert message.
     * 
     * @param string $message
     * @param int    $statusCode
     * @param int    $code
     * 
     * return ResponseInterface
     */
    protected function alert(string $message = '', int $statusCode = 0, int $code = 0): ResponseInterface
    {
        $this->json['__alert'] = [
            'size'    => SYSTEM_HANDLE_ERRORS ? 'medium' : 'large',
            'title'   => _t('Alert'),
            'content' => $message,
            'code'    => $code,
            'buttons' => [['caption' => _t('Close'), 'type' => 'close']],
        ];

        return $this->response($statusCode);
    }
}
