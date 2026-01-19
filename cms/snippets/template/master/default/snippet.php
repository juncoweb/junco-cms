<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Psr\Http\Message\ResponseInterface;

class template_master_default_snippet extends Template
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->view    = __DIR__ . '/view.html.php';
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
        $this->content = $message;
        $response = $this->response();

        if ($code >= 100 && $code <= 599) {
            $response = $response->withStatus($code);
        }

        return $response;
    }
}
