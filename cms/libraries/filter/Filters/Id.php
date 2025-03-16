<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Filter\Filters;

class Id extends FilterAbstract
{
	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->type = 'int';
		$this->default  = 0;
		$this->argument = [
			'filter' => FILTER_VALIDATE_INT,
			'options' => ['min_range' => 0, 'default' => 0]
		];
	}
}
