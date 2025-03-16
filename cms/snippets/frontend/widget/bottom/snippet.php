<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

class widget_frontend_bottom_snippet
{
	// vars
	protected $rows = [];

	/**
	 * Section
	 * 
	 * @param array $section
	 */
	public function section(array $section)
	{
		$this->rows[] = array_merge([
			'title'		=> '',
			'content'	=> '',
			'css'		=> '',
			'container'	=> true
		], $section);
	}

	/**
	 * Render
	 */
	public  function render()
	{
		$html = '';
		foreach ($this->rows as $row) {
			if ($row['title']) {
				$row['title'] = '<h3 class="title">' . $row['title'] . '</h3>';
			}
			if ($row['container'] === true) {
				$row['container'] = 'container';
			}

			$html .= "\n\t" . '<section class="widget' . ($row['css'] ? ' ' . $row['css'] : '') . '">'
				. '<div' . ($row['container'] ? ' class="' . $row['container'] . '"' : '') . '>'
				.   $row['title']
				.   $row['content']
				. '</div>'
				. '</section>';
		}

		return $html;
	}
};
