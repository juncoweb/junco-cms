<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;
use Junco\Http\Client;

class AdminLanguageTranslationsModel extends Model
{
	/**
	 * Get
	 */
	public function getIndexData()
	{
		// data
		$this->filter(POST, ['id' => 'array:first']);

		return [
			'data' => ['search' => $this->data['id']]
		];
	}

	/**
	 * Get
	 */
	public function getListData()
	{
		// data
		$this->filter(POST, [
			'search' => 'text',
			'page'	 => 'id',
		]);

		// vars
		$url  = 'https://www.juncoweb.com/index.php';
		$args = ['goto' => 'translations/json', 'format' => 'json'];

		if ($this->data['search']) {
			$args['search'] = $this->data['search'];
		}
		if ($this->data['page']) {
			$args['page'] = $this->data['page'];
		}
		try {
			$content = (new Client)
				->get($url, ['data' => $args])
				->getBody();

			$json = json_decode($content, true);

			if (!empty($json['__alert'])) {
				throw new Exception($json['__alert']);
			}
		} catch (Exception $e) {
			return ['error' => $e->getMessage() ?: 'Error!'];
		}
		// query
		$pagi = new Pagination();
		$pagi->num_rows = $json['num_rows'];
		$pagi->rows_per_page = $json['rows_per_page'];
		$pagi->calculate();

		return $this->data + ['pagi' => $pagi, 'rows' => $json['rows']];
	}

	/**
	 * Get
	 */
	public function getDownloadData()
	{
		// data
		$this->filter(POST, [
			'id' => 'id|array:first|required:abort',
		]);

		return $this->data;
	}

	/**
	 * download
	 */
	public function download()
	{
		// data
		$this->filter(POST, ['id' => 'id|required:abort']);

		// vars
		$locale	= (new LanguageHelper)->getLocale();
		$url	= sprintf('https://www.juncoweb.com/translations/download?id=%d&format=blank', $this->data['id']);

		(new Client)
			->get($url)
			->moveTo($locale, $filename);

		(new Archive($locale))->extract($filename, '', true);
		(new LanguageHelper)->refresh();
	}
}
