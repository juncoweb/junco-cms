<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

class SystemException extends Exception
{
	public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
	{
		if (is_numeric($message)) {
			$code    = (int)$message;
			$message = app('debugger')->getMessageFromCode($message);
		}

		parent::__construct($message, $code, $previous);
	}
}
