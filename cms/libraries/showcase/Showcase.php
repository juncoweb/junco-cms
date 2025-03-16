<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Showcase\Contract\ShowcaseInterface;

class Showcase
{
	/**
	 * Get
	 */
	public static function get(string $snippet = ''): ShowcaseInterface
	{
		return snippet('showcase', $snippet);
	}
}
