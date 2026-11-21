<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Frontlist\FrontlistBase;

class frontlist_master_three_snippet extends FrontlistBase
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
        // assets
        $this->assets(['css' => 'assets/frontlist.min.css']);

        $html = '';

        if ($this->rows) {
            $image_url = $this->imageset?->getImage(150);

            foreach ($this->rows as $row) {
                $title = $row['title'];

                if ($row['image']) {
                    if ($image_url) {
                        $row['image'] = $image_url . $row['image'];
                    }

                    $row['media_html'] = '<img src="' . $row['image'] . '" alt="' . $row['title'] . '" />';
                }

                if ($row['url']) {
                    if ($row['media_html']) {
                        $row['media_html'] = '<a href="' . $row['url'] . '" tabindex="-1" aria-hidden="true">' . $row['media_html'] . '</a>';
                    }
                    $title = '<a href="' . $row['url'] . '">' . $title . '</a>';
                }
                $html .= '<article control-row="' . $row['id'] . '">';

                if ($row['media_html']) {
                    if ($row['description']) {
                        $row['media_html'] .= '<div class="article-desc">' . $row['description'] . '</div>';
                    }

                    $html .= '<div class="article-media">' . $row['media_html'] . '</div>';
                }

                $html .= '<div class="article-container">';
                $html .= '<h2>' . $title . '</h2>';

                if ($row['author']) {
                    $html .= '<div class="article-author">' . $row['author'] . '</div>';
                }

                if ($row['date']) {
                    $html .= '<div class="article-date">' . $row['date'] . '</div>';
                }

                if ($row['labels']) {
                    $html .= '<div class="article-footer">' . $this->renderLabels($row['labels']) . '</div>';
                }

                if ($row['footer'] || $row['rating']) {
                    $html .= '<div class="article-footer">' . $row['rating'] . $row['footer'] . '</div>';
                }

                if ($row['button']) {
                    $html .= '<div class="article-button">' . $row['button'] . '</div>';
                }

                $html .= '</div></article>';
            }

            $html = '<div class="grid grid-3 grid-responsive frontlist-three">' . $html . "\n" . '</div>' . "\n";
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
    protected function renderLabels(array $labels): string
    {
        return implode(' ', array_map(function ($label) {
            return '<a href="' . $label['url'] . '" class="badge badge-secondary">' . $label['name'] . '</a>';
        }, $labels));
    }
}
