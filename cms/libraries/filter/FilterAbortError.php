<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

/**
 * Error
 * 
 * @ Used only in abort.
 * @ Caution: change the references to the file and the line
 * 
 */
class FilterAbortError extends Error
{
	/**
	 * Constructor
	 */
	public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
	{
		$debug = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5);
		$this->file	= $debug[4]['file'];
		$this->line	= $debug[4]['line'];

		parent::__construct($message, $code, $previous);
	}
}
