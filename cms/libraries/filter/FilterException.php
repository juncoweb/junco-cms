<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Http\Exception\HttpException;

class FilterException extends HttpException
{
    /**
     * Constructor
     */
    public function __construct(string $message = '')
    {
        parent::__construct(422, $message);

        $debug = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5);
        $this->file = $debug[4]['file'];
        $this->line = $debug[4]['line'];
    }
}
