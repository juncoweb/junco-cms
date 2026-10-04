<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Backlist\Contract\FiltersInterface;
use Junco\Form\FilterElements;

class backlist_master_default_filters extends FilterElements implements FiltersInterface
{
    //
    protected int    $order     = -1;
    protected int    $idxOrder  = 0;
    protected string $sortTitle = '';
    protected string $sortTag   = '';

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
        if ($sort === 'asc') {
            $this->sortTitle = _t('Ascending');
            $icon = 'fa-solid fa-caret-up';
        } else {
            $this->sortTitle = _t('Descending');
            $icon = 'fa-solid fa-caret-down';
        }

        $this->order = $order;
        $this->sortTag = '<i class="' . $icon . ' color-primary" aria-hidden="true"></i>';

        $this->hidden('sort', $sort);
        $this->hidden('order', $order);
    }

    /**
     * Adds filter controls to a table header.
     * 
     * @param string $label
     * 
     * @return string
     */
    public function sort_h(string $label = ''): string
    {
        $this->idxOrder++;
        $status = ($this->order === $this->idxOrder);
        $title = false === strpos($label, '<')
            ? $label
            : $this->extractTitle($label);

        $title = sprintf(_t('Order by %s, %s'), $title, $this->sortTitle);

        return '<a href="javascript:void(0)" role="button" control-filter="sort" data-value="' . $this->idxOrder . '" title="' . $title . '" class="kl-sort" aria-pressed="' . ($status ? 'true' : 'false') . '">'
            . '<span class="visually-hidden">' . $title . '</span>'
            . '<span aria-hidden="true">' . $label . '</span>'
            . ($status ? $this->sortTag : '')
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

    /**
     * Extract
     */
    protected function extractTitle(string $html): string
    {
        return preg_match('/title="(.*?)"/', $html, $match)
            ? $match[1]
            : _t('Unknow');
    }
}
