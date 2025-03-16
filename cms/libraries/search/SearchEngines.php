<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

class SearchEngines
{
	private $engines	= [];
	private $current	= '';
	private $fn			= null;
	public  $num_rows	= 0;

	/**
	 * Constructor
	 */
	public function __construct($current)
	{
		$plugins = config('search.engines_plugins');
		if ($plugins) {
			if (!$current) {
				$current = 'search';
			}
			if (!in_array($current, $plugins)) {
				$current = $plugins[0];
			}

			$this->current = $current;

			if ($plugins = Plugins::get('search_engine', 'load', $plugins)) {
				$plugins->run($this);
			}
		}
	}

	/**
	 * Add
	 */
	public function add($label, $title, $fn)
	{
		$this->engines[] = [$label, $title];
		$this->num_rows++;

		if ($this->current == $label) {
			$this->fn = &$fn;
		}
	}

	/**
	 * Render
	 */
	public function render()
	{
		$html	= '';
		foreach ($this->engines as $row) {
			$html .= '<label><input type="radio" name="engine" value="' . $row[0] . '" class="input-radio"' . ($row[0] == $this->current ? ' checked' : '') . ' /> ' . $row[1] . '</label>';
		}

		return $html;
	}

	/**
	 * Results
	 */
	public function results($search)
	{
		return call_user_func($this->fn, $search);
	}
}
