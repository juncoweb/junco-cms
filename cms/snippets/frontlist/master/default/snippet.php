<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Frontlist\FrontlistBase;

class frontlist_master_default_snippet extends FrontlistBase
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
			$col3 = $this->hasCol3();

			foreach ($this->rows as $row) {
				$html .= "\n\t" . '<tr control-row="' . $row['id'] . '">';
				$title = $row['title'];

				if ($row['image']) {
					$row['image_html'] = '<img src="' . $row['image'] . '" alt="' . $row['title'] . '"/>';
				}
				if ($row['url']) {
					if ($row['image_html']) {
						$row['image_html'] = '<a href="' . $row['url'] . '" title="' . $row['title'] . '">' . $row['image_html'] . '</a>';
					}
					$title = '<a href="' . $row['url'] . '" title="' . $row['title'] . '">' . $title . '</a>';
				}
				if ($row['image_html']) {
					$html .= '<td class="col-img"><span>' . $row['image_html'] . '</span></td>';
				}
				$html .= '<td class="col-bdy">'
					. '<div class="fl-title"><h3>' . $title . '</h3></div>';

				if ($row['author']) {
					$html .= '<div class="fl-author">' . $row['author'] . '</div>';
				}
				if ($row['date']) {
					$html .= '<div class="fl-date">' . $row['date'] . '</div>';
				}
				if ($row['description']) {
					$html .= '<div class="fl-description">' . $row['description'] . '</div>';
				}
				if ($row['labels']) {
					$html .= '<div class="fl-footer">' . implode(' ', array_map(function ($label) {
						return '<a href="' . $label['url'] . '" class="badge badge-secondary">' . $label['name'] . '</a>';
					}, $row['labels'])) . '</div>';
				}
				if ($row['footer']) {
					$html .= '<div class="fl-footer">' . $row['footer'] . '</div>';
				}

				$html .= '</td>';

				if ($col3) {
					$html .= '<td class="col-opt">' . $row['rating'] . $row['button'] . '</td>';
				}

				$html .= '</tr>';
			}

			$html = '<table class="frontlist"><tbody>' . $html . "\n" . '</tbody></table>' . "\n";
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

	/**
	 * Has 
	 */
	protected function hasCol3(): bool
	{
		foreach ($this->rows as $row) {
			if ($row['button'] || $row['rating']) {
				return true;
			}
		}

		return false;
	}
}
