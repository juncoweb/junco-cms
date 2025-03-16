<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

defined('PAGINATION_PAGE') or
	define('PAGINATION_PAGE', 'page');

class Pagination
{
	// vars
	public int $cur_page		= 0;
	public int $rows_per_page	= 15;
	public int $num_rows		= 0;
	public int $num_pages		= 1;
	public int $offset			= 0;
	protected string $snippet	= '';
	protected array  $rows		= [];

	// navigation
	public string $nav_href		= 'javascript:void(0)';
	public string $nav_disabled	= 'disabled';
	public string $nav_active	= 'active';

	/**
	 * Slice
	 *
	 * @Performs the paging of an array.
	 * 
	 * @param array  $rows
	 */
	public function slice($rows)
	{
		$this->num_rows = count($rows);
		$this->calculate();
		$this->rows = array_slice($rows, $this->offset, $this->rows_per_page);
	}

	/**
	 * calculate
	 */
	public function calculate()
	{
		if (!$this->cur_page) {
			$this->cur_page	=
				Filter::input(GET, PAGINATION_PAGE, 'id')
				?: Filter::input(POST, PAGINATION_PAGE, 'id')
				?: 1;
		}
		if ($this->cur_page < 1) {
			$this->cur_page	= 1;
		}

		$this->num_pages	= ceil($this->num_rows / $this->rows_per_page);
		$this->cur_page		= $this->num_pages > 1
			? ($this->cur_page < $this->num_pages ? $this->cur_page : $this->num_pages)
			: 1;
		$this->offset		= ($this->cur_page - 1) * $this->rows_per_page;
	}

	/**
	 * Url
	 *
	 * @Build the url by adding the page number. 
	 * 
	 */
	public function setRows(array $rows)
	{
		$this->rows = $rows;
	}

	/**
	 * Fetch all results.
	 */
	public function fetchAll()
	{
		return $this->rows;
	}

	/**
	 * Url
	 *
	 * @Build the url by adding the page number. 
	 * 
	 */
	public function url(?string $route = null, array $args = [], $hash = '')
	{
		if (array_filter($args)) {
			$args = array_filter($args);
		}
		$args[PAGINATION_PAGE] = '{{page}}';
		$this->nav_href	= url($route, $args) . $hash;
	}

	/**
	 * Build
	 *
	 * @build the html of the page.
	 *
	 * @param array  $tags  
	 * @param array  $arrows  
	 * @param int    $num_links
	 */
	public function build(
		array   $tags,
		array   $arrows,
		int     $num_links = -1,
		?string $extremes = null
	) {
		$data = [];
		if ($arrows) {
			// nav arrows
			if ($this->cur_page != 1) {
				$data['first'] = 1;
				$data['prev']  = $this->cur_page - 1;
			} else {
				$data['first']	=
					$data['prev']	= false;
			}

			if ($this->cur_page < $this->num_pages) {
				$data['next'] = $this->cur_page + 1;
				$data['last'] = $this->num_pages;
			} else {
				$data['next'] =
					$data['last'] = false;
			}
			foreach ($arrows as $key => $arrow) {
				if ($data[$key]) {
					$data[$key] = strtr($tags[0], ['{{page}}' => $data[$key], '{{placeholder}}' => $arrow, '{{key}}' => $key]);
				} else {
					$data[$key] = strtr($tags[1], ['{{style}}' => $this->nav_disabled, '{{placeholder}}' => $arrow, '{{key}}' => $key]);
				}
			}
		}

		if ($num_links > -1) {
			// vars
			$from	= $this->cur_page - $num_links;
			$to		= $this->cur_page + $num_links;
			$html	= '';

			if (count($tags) > 2) {
				$tags[0] = $tags[2];
				$tags[1] = $tags[3];
			}
			if ($from < 1) {
				$to  -= $from - 1;
				$from = 1;
			}
			if ($to > $this->num_pages) {
				$from -= $to - $this->num_pages;
				$to    = $this->num_pages;
				if ($from < 1) {
					$from = 1;
				}
			}

			for ($i = $from; $i <= $to; $i++) {
				if ($i != $this->cur_page) {
					$html .= strtr($tags[0], ['{{page}}' => $i, '{{placeholder}}' => $i]);
				} else {
					$html .= strtr($tags[1], ['{{style}}' => $this->nav_active, '{{placeholder}}' => $i]);
				}
			}

			//
			$data['from']       = $from;
			$data['to']         = $to;
			$data['numeration'] = $html;

			if ($extremes !== null) { // build: 1 ...   ... 99
				if ($from != 1) {
					$data['first_number'] = strtr($tags[0], ['{{page}}' => 1, '{{placeholder}}' => 1]) . $extremes;
				} else {
					$data['first_number'] = '';
				}
				if ($to != $this->num_pages) {
					$data['last_number'] = $extremes . strtr($tags[0], ['{{page}}' => $this->num_pages, '{{placeholder}}' => $this->num_pages]);
				} else {
					$data['last_number'] = '';
				}
			}
		}

		return $data;
	}

	/**
	 * Sets the snippet to use.
	 * 
	 * @param string $snippet
	 * 
	 */
	public function snippet(string $snippet = ''): void
	{
		$this->snippet = $snippet;
	}

	/**
	 * Render
	 */
	public function render(): string
	{
		return snippet('pagination', $this->snippet)->render($this);
	}

	/**
	 * To string representation.
	 */
	public function __toString(): string
	{
		return $this->render();
	}
}
