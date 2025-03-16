<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Filter\Filters;

class Uuid extends FilterAbstract
{
	const PATTERN = '/^[\da-f]{8}-[\da-f]{4}-[\da-f]{4}-[\da-f]{4}-[\da-f]{12}$/iD';

	/**
	 * Constructor
	 * 
	 * @param string|array|null $filter_value
	 */
	public function __construct(string|array|null $filter_value = null)
	{
		$this->type    = 'string';
		$this->default = false;

		$this->callback[] = function (&$value) {
			if (!is_string($value) || !preg_match(self::PATTERN, $value)) {
				$value = false;
			}
		};
	}
}
