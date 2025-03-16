<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

/**
 * Collector
 */
class SearchCollector
{
	// vars
	protected $rows = [];

	/**
	 * Get
	 */
	public function set($title = false)
	{
		$row = [
			'title' => $title,
			'results' => [],
		];

		$this->rows[] = &$row;
		return new SearchResults($row['results']);
	}

	/**
	 * Set
	 */
	public function get()
	{
		return $this->rows;
	}
}

/**
 * Results
 */
class SearchResults
{
	public $results = null;

	/**
	 * Constructor
	 */
	public function __construct(&$results)
	{
		$this->results = &$results;
	}

	/**
	 * add
	 */
	public function add($header = false, $url = false)
	{
		$this->results[] = [
			'header'	=> $header,
			'url'		=> $url,
		];
	}
}


return function (&$engines) {
	$engines->add(
		'search',
		_t('Site'),
		function ($search) {
			if (!$search) {
				return '<p class="se-msg">' . _t('Please enter the text to start the search.') . '</p>';
			}

			$config = config('search-engine');
			if (($length = strlen($search)) < $config['search-engine.min_chars']) {
				return '<p class="se-msg">' . sprintf(_t('Please enter a minimum of %d characters to search.'), $config['search-engine.min_chars']) . '</p>';
			}
			if ($length > $config['search-engine.max_chars']) {
				return '<p class="se-msg">' . sprintf(_t('You\'ve exceeded the maximum limit of %d characters.'), $config['search-engine.max_chars']) . '</p>';
			}


			# plugins
			$plugins = Plugins::get('search', 'plugin', $config['search-engine.search_plugins']);
			if ($plugins) {
				$collector = new SearchCollector();
				$plugins->run($collector, $search);
				$rows = $collector->get();
			} else {
				return '<p class="se-msg">' . _t('Empty list') . '</p>';
			}
			if (empty($rows)) {
				return '<p class="se-msg">' . _t('Empty list') . '</p>';
			}

			$html = '';
			foreach ($rows as $row) {
				$html .= "\t" . '<h3>' . $row['title'] . '</h3>' . "\n"
					. "\t" . '<ul>' . "\n";
				foreach ($row['results'] as $result) {
					$html .= "\t\t" . '<li><a href="' . $result['url'] . '">' . $result['header'] . '</a></li>' . "\n";
				}
				$html .= "\t" . '</ul>' . "\n\n";
			}

			return $html;
		}
	);
};
