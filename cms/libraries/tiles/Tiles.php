<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

class Tiles
{
	/**
	 * Get
	 */
	public static function get(string $snippet = ''): TilesInterface
	{
		return snippet('tiles', $snippet);
	}
}
