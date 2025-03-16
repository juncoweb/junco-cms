<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;

class AdminLanguageModel extends Model
{
	/**
	 * Get
	 */
	public function getListData()
	{
		// data
		$this->filter(POST, ['search' => 'text']);

		// vars
		$dir	= (new LanguageHelper())->getLocale();
		$cdir   = scandir($dir);
		$rows   = [];

		if ($cdir) {
			$filter		= $this->data['search'] && preg_match('@[\w_]+@', preg_quote($this->data['search'], '@')) ? '@' . $this->data['search'] . '@i' : '';
			$availables = app('language')->getAvailables();
			$curLang	= app('language')->getCurrent();

			foreach ($cdir as $has) {
				if (
					$has != '.'
					&& $has != '..'
					&& is_dir($dir . $has)
					&& (!$filter || preg_match($filter, $has))
				) {
					$json = $dir . $has . '/' . $has . '.json';
					if (is_file($json)) {
						$json = json_decode(file_get_contents($json), true);
					}

					$rows[] = [
						'id' => $has,
						'name' => $json['name'] ?? $has,
						'selected' => ($has == $curLang ? 'yes' : 'no'),
						'status' => in_array($has, $availables) ? 'enabled' : 'disabled',
					];
				}
			}
		}

		return $this->data + ['rows' => $rows];
	}


	/**
	 * Get
	 */
	public function getEditData()
	{
		// data
		$this->filter(POST, ['id' => 'array:first|required:abort']);

		// query
		$locale	= (new LanguageHelper)->getLocale();
		$json	= $locale . $this->data['id'] . '/' . $this->data['id'] . '.json';
		$json	= is_file($json)
			? json_decode(file_get_contents($json), true)
			: false;

		// security
		$json or abort();
		$json['language'] = $this->data['id'];

		return [
			'title' => _t('Edit'),
			'values' => $json,
		];
	}

	/**
	 * Get
	 */
	public function getConfirmDuplicateData()
	{
		// data
		$this->filter(POST, ['id' => 'array:first|required:abort']);

		// query
		is_dir((new LanguageHelper)->getLocale() . $this->data['id']) or abort();

		return ['language' => $this->data['id']];
	}

	/**
	 * Get
	 */
	public function getConfirmSelectData()
	{
		// data
		$this->filter(POST, ['id' => 'array:first|required:abort']);

		return $this->data;
	}

	/**
	 * Get
	 */
	public function getConfirmDeleteData()
	{
		// data
		$this->filter(POST, ['id' => 'array|required:abort']);

		return $this->data;
	}

	/**
	 * Get
	 */
	public function getConfirmDistributeData()
	{
		// data
		$this->filter(POST, ['id' => 'array:first|required:abort']);

		return $this->data;
	}
}
