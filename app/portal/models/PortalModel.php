<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;

class PortalModel extends Model
{
	/**
	 * Get
	 */
	public function getIndexData()
	{
		$config = config('portal');

		return [
			'snippet' => $config['portal.snippet'],
			'plugins' => $config['portal.plugins'],
			'options' => $config['portal.options']
		];
	}
}
