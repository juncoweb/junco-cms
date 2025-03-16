<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Filter\Filters;

class Multiline extends FilterAbstract
{
	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->type = 'string';
		$this->default  = '';
		$this->argument = [
			'filter' => FILTER_SANITIZE_SPECIAL_CHARS
		];
		//
		$this->callback[] = function (&$value) {
			$value = nl2br(trim(str_replace(["&#10;", "&#13;"], ["\n", "\r"], $value)));
		};
	}
}
