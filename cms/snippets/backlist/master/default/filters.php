<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Backlist\Contract\FiltersInterface;
use Junco\Form\FilterElements;

class backlist_master_default_filters extends FilterElements implements FiltersInterface
{
	//
	protected int    $curOrder	= -1;
	protected int    $idxOrder	= 0;
	protected string $curSort	= '';

	/**
	 * Sort
	 * 
	 * @param string $sort
	 * @param int    $order
	 * 
	 * @return void
	 */
	public function sort(string $sort = '', int $order = 0): void
	{
		$this->hidden('sort', $this->curSort = $sort);
		$this->hidden('order', $this->curOrder = $order);
	}

	/**
	 * Adds filter controls to a table header.
	 * 
	 * @param string $header
	 * 
	 * @return string
	 */
	public function sort_h(string $title = ''): string
	{
		$sort = $this->curOrder === (++$this->idxOrder)
			? ' <i class="' . ($this->curSort === 'asc' ? 'fa-solid fa-caret-up' : 'fa-solid fa-caret-down') . ' color-primary"></i>'
			: '';

		return '<a href="javascript:void(0)" control-filter="sort" data-value="' . $this->idxOrder . '" class="kl-sort">'
			.  $title . $sort
			. '</a>';
	}

	/**
	 * Render
	 * 
	 * @return string
	 */
	public function render(): string
	{
		$html = $this->hidden;
		foreach ($this->rows as $content) {
			$html .= '<div class="btn-group btn-small">' . $content . '</div>';
		}

		// freeing memory
		$this->hidden = '';
		$this->rows = [];

		return '<div class="backlist-filters" backlist-filters><form>' . $html . '</form></div>';
	}
}
