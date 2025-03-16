<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;

class SearchModel extends Model
{
	/**
	 * Save
	 */
	public function getIndexData()
	{
		// data
		$this->filter(GET, [
			'q'      => 'text',
			'engine' => ''
		]);

		return [
			'engines' => new SearchEngines($this->data['engine']),
			'search' => $this->data['q'],
			'options' => config('search.options')
		];
	}
}
