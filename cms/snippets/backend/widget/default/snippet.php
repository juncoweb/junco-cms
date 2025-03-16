<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

class widget_backend_default_snippet
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
			'content'	=> '',
			'css'		=> ''
		], $section);
	}

	/**
	 * Render
	 */
	public function render()
	{
		$html = '';
		foreach ($this->rows as $row) {
			$html .= '<div' . ($row['css'] ? '  class="' . $row['css'] . '"' : '') . '>'
				.  $row['content']
				. '</div>';
		}

		return $html;
	}
}
