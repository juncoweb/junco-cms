<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;

class FrontLanguageModel extends Model
{
	/**
	 * Get
	 */
	public function getContentData()
	{
		// vars
		$languages = (new LanguageHelper)->getAvailables();

		if (curuser()->isAdmin()) {
			$languages = array_merge(['Clear'], $languages);
		}

		return [
			'languages' => $languages,
			'values' => ['lang' => app('language')->getCurrent()],
		];
	}
}
