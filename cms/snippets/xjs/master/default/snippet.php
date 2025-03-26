<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Responder\ResponderBase;
use Psr\Http\Message\ResponseInterface;

class xjs_master_default_snippet extends ResponderBase
{
    // vars
    protected $json = [];

    /**
     * Creates a simplified response with a message.
     * 
     * @param string $message
     * @param int    $code
     * 
     * @return ResponseInterface
     */
    public function message(string $message = '', int $code = 0, $data = null): ResponseInterface
    {
        if ($message) {
            $this->json['message'] = $message;
        }
        if ($code) {
            $this->json['code'] = $code;
        }
        if ($data !== null) {
            $this->json['data'] = $data;
        }

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
        $this->json['__alert'] = [
            'size'        => SYSTEM_HANDLE_ERRORS ? 'medium' : 'large',
            'title'        => _t('Alert'),
            'content'    => $message,
            'code'        => $code,
            'buttons'    => [['caption' => _t('Close'), 'type' => 'close']],
        ];

        return $this->response();
    }

    /**
     * Create a response.
     * 
     * @return ResponseInterface
     */
    public function response(): ResponseInterface
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

        return $this->createJsonResponse($this->json);
    }
}
