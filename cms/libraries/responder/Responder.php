<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Responder\Contract\AjaxJsonInterface;
use Junco\Responder\Contract\AjaxTextInterface;
use Junco\Responder\Contract\HttpBlankInterface;

class Responder
{
	/**
	 * 
	 */
	public static function asAjaxJson(): AjaxJsonInterface
	{
		return snippet('responder#ajax_json');
	}
	/**
	 * 
	 */
	public static function asAjaxText(): AjaxTextInterface
	{
		return snippet('responder#ajax_text');
	}

	/**
	 * 
	 */
	public static function asHttpBlank(): HttpBlankInterface
	{
		return snippet('responder#http_blank');
	}
}
