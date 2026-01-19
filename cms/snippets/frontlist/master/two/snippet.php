<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
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

                $html .= '<article control-row="' . $row['id'] . '">';
                if ($row['image_html']) {
                    $html .= '<div class="grid gap-0 article-wrapper">';
                    $html .= '<div class="article-media">' . $row['image_html'] . '</div>';
                } else {
                    $html .= '<div class="article-wrapper">';
                }

                $html .= '<div class="article-container">';
                $html .= '<div><h3>' . $title . '</h3></div>';

                if ($row['date']) {
                    $html .= '<div class="article-date">' . $row['date'] . '</div>';
                }

                if ($row['author']) {
                    $html .= '<div class="article-author">' . $row['author'] . '</div>';
                }

                if ($row['description']) {
                    $html .= '<div class="article-description">' . $row['description'] . '</div>';
                }

                if ($row['button']) {
                    $html .= '<div class="article-button">' . $row['button'] . '</div>';
                }

                if ($row['labels']) {
                    $html .= '<div class="article-labels">' . $this->renderLabels($row['labels']) . '</div>';
                }

                $html .= '</div></div>';

                if ($row['footer'] || $row['rating']) {
                    $html .= '<div class="article-footer">' . $row['rating'] . $row['footer'] . '</div>';
                }
                $html .= '</article>';
            }

            $html = '<div class="grid grid-medium-box grid-responsive frontlist-two">' . $html . "\n" . '</div>' . "\n";
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
     * Render
     * 
     * @return string
     */
    protected function renderLabels(array $labels): string
    {
        return sprintf(_t('Labels %s'), implode(' ', array_map(function ($label) {
            return '<a href="' . $label['url'] . '">' . $label['name'] . '</a>';
        }, $labels)));
    }
}
