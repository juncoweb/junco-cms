<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
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
                $html .= "\n\t" . '<article class="grid grid-2 grid-responsive" control-row="' . $row['id'] . '">';
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
                    $html .= '<div class="article-media">' . $row['image_html'] . '</div>';
                }

                $html .= '<div class="article-container">';
                $html .= '<div class="article-title"><h3>' . $title . '</h3></div>';

                if ($row['author']) {
                    $html .= '<div class="article-author">' . $row['author'] . '</div>';
                }

                if ($row['date']) {
                    $html .= '<div class="article-date">' . $row['date'] . '</div>';
                }

                if ($row['description']) {
                    $html .= '<div class="article-desc">' . $row['description'] . '</div>';
                }

                if ($row['labels']) {
                    $html .= '<div class="article-footer">' . $this->renderLabels($row['labels']) . '</div>';
                }

                if ($row['footer']) {
                    $html .= '<div class="article-footer">' . $row['footer'] . '</div>';
                }

                $html .= '</div>';

                if ($col3) {
                    $html .= '<div class="article-action">' . $row['rating'] . $row['button'] . '</div>';
                }

                $html .= '</article>';
            }

            $html = '<div class="frontlist">' . $html . "\n" . '</div>' . "\n";
            $this->rows = []; // freeing memory
        } else {
            $html = '<div class="empty-list">' . ($this->empty_list ?: _t('Empty list')) . '</div>' . "\n";
        }

        if (isset($this->filters)) {
            $html = $this->filters->render() . "\n" . $html;
        }

        if ($pagi) {
            $html .= '<div class="article-pagination">' . $pagi . '</div>' . "\n";
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

    /**
     * Has 
     */
    protected function renderLabels(array $labels): string
    {
        return implode(' ', array_map(function ($label) {
            return '<a href="' . $label['url'] . '" class="badge badge-small badge-secondary">' . $label['name'] . '</a>';
        }, $labels));
    }
}
