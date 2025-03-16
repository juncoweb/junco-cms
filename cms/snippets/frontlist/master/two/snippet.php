<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Frontlist\FrontlistBase;

class frontlist_master_two_snippet extends FrontlistBase
{
	/**
	 * Render
	 * 
	 * @param string $pagi
	 * 
	 * @return string
	 */
	public function render(string $pagi = ''): string
	{
		$html = '';

		if ($this->rows) {
			foreach ($this->rows as $row) {
				$title = $row['title'];

				if ($row['image']) {
					$row['image_html'] = '<img src="' . $row['image'] . '" alt="' . $row['title'] . '" />';
				}
				if ($row['url']) {
					if ($row['image_html']) {
						$row['image_html'] = '<a href="' . $row['url'] . '" title="' . $row['title'] . '">' . $row['image_html'] . '</a>';
					}

					$title = '<a href="' . $row['url'] . '" title="' . $row['title'] . '">' . $title . '</a>';
				}

				$html .= '<article control-row="' . $row['id'] . '"><div class="fl-entry">';
				if ($row['image_html']) {
					$html .= '<div class="fl-thumbnail">' . $row['image_html'] . '</div>';
				}

				$html  .= '<div>';
				$html .= '<div><h3>' . $title . '</h3></div>';
				if ($row['author']) {
					$html .= '<div class="fl-author">' . $row['author'] . '</div>';
				}
				if ($row['date']) {
					$html .= '<div class="fl-date">' . $row['date'] . '</div>';
				}
				if ($row['description']) {
					$html .= '<div class="fl-description">' . $row['description'] . '</div>';
				}
				if ($row['button']) {
					$html .= '<div class="fl-button">' . $row['button'] . '</div>';
				}
				if ($row['labels']) {
					$html .= '<div class="fl-footer">' . implode(' ', array_map(function ($label) {
						return '<a href="' . $label['url'] . '" class="badge badge-secondary">' . $label['name'] . '</a>';
					}, $row['labels'])) . '</div>';
				}
				$html .= '</div></div>';

				if ($row['footer'] || $row['rating']) {
					$html .= '<div class="fl-footer">' . $row['rating'] . $row['footer'] . '</div>';
				}
				$html .= '</article>';
			}

			$html = '<div class="frontlist-two">' . $html . "\n" . '</div>' . "\n";
			$this->rows = []; // freeing memory

		} else {
			$html = '<div class="empty-list">' . ($this->empty_list ?: _t('Empty list')) . '</div>' . "\n";
		}

		if (isset($this->filters)) {
			$html = $this->filters->render() . "\n" . $html;
		}

		if ($pagi) {
			$html .= '<div class="fl-pagination">' . $pagi . '</div>' . "\n";
		}

		return $html;
	}
}
