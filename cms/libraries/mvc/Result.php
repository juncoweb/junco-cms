<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Mvc;

class Result
{
    // vars
    protected string $message;
    protected int    $statusCode;
    protected int    $code;
    protected mixed  $data;

    /**
     * Constructor
     */
    public function __construct(int $statusCode, string $message = '', int $code = 0, mixed $data = null)
    {
        $this->message    = ($message ?: _t('The task has been completed successfully.'));
        $this->statusCode = $statusCode;
        $this->code       = $code;
        $this->data       = $data;
    }

    /**
     * Set
     */
    public function setMessage(string $message): self
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Set
     */
    public function setStatusCode(int $statusCode): self
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    /**
     * Set
     */
    public function setCode(int $code): self
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Set
     */
    public function setData(mixed $data): self
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Reload Page
     * 
     * @return self
     */
    public function reloadPage(): self
    {
        $this->data = ['__reloadPage' => 1];
        return $this;
    }

    /**
     * Redirect to URL
     * 
     * @param string $url
     * 
     * @return self
     */
    public function redirectTo(string $url = ''): self
    {
        $this->data = ['__redirectTo' => $url];
        return $this;
    }

    /**
     * Go back
     * 
     * @return self
     */
    public function goBack(): self
    {
        $this->data = ['__goBack' => 1];
        return $this;
    }

    /**
     * Get
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Get
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Get
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * Get
     */
    public function getData(): mixed
    {
        return $this->data;
    }

    /**
     * Render
     */
    public function render(): array
    {
        $output = [
            'message' => $this->message,
            'code'    => $this->code,
        ];

        if ($this->data !== null) {
            $output['data'] = $this->data;
        }

        return $output;
    }
}
