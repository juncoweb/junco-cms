<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Showcase\ShowcaseBase;

class showcase_master_default_snippet extends ShowcaseBase
{
    // vars
    protected array $data = [
        'id'           => '',
        'url'          => '',
        'edit_url'     => '',
        'title'        => '',
        'image'        => '',
        'image_html'   => '',
        'date'         => '',
        'summary'      => '',
        'description'  => '',
        'button'       => '',
        'info'         => [],
        'labels'       => [],
        'authors'      => [],
        'avg_ratings'  => null,
        'num_visits'   => -1,
        'num_comments' => -1,
    ];

    /**
     * Render
     */
    public function render(): string
    {
        $data = $this->data;
        $this->data = []; // free memory
        $this->assets(['css' => 'assets/showcase.min.css']);
        $this->getOption('plugins')?->run(
            $data['description'],
            $data['url'],
            $data['title'],
            $data['summary'] ?: $data['description'],
            $data['image']
        );

        // info
        $info = [];

        if ($data['date']) {
            if (is_string($data['date'])) {
                $data['date'] = new Date($data['date']);
            }
            $info[] = '<div class="article-date">' . $data['date']->format(_t('Y-M-d')) . '</div>';
        }

        if ($data['authors']) {
            $info[] = '<div class="article-author">'
                . sprintf(_t('By %s'), $this->renderLinks($data['authors']))
                . '</div>';
        }

        if ($data['labels']) {
            $info[] = '<div class="article-labels">'
                . sprintf(_t('Labels %s'), $this->renderLinks($data['labels']))
                . '</div>';
        }

        $line = [];
        if ($data['avg_ratings'] !== null) {
            $line[] = snippet('rating', 'utils')->render($data['avg_ratings'], ['color' => 'warning']);
        }

        if ($data['num_visits'] > -1) {
            $line[] = sprintf(_t('%d visits'), $data['num_visits']);
        }

        if ($data['num_comments'] != -1) {
            $line[] = sprintf(_t('%d comments'), $data['num_comments']);
        }

        if ($line) {
            $data['info'][] = implode(' | ', $line);
        }

        if ($data['info']) {
            foreach ($data['info'] as $value) {
                $info[] = '<div>' . $value . '</div>';
            }
        }

        $title = $data['title'];
        if ($data['url']) {
            $title = '<a href="' . $data['url'] . '">' . $title . '</a>';
        }
        if ($data['edit_url']) {
            $data['edit_url'] = ' <a href="' . $data['edit_url'] . '" class="edit" title="' . _t('Edit') . '"><i class="fa-solid fa-pencil font-small"></i></a>';
        }

        // content
        $content = '';
        if ($data['button']) {
            $content .= "\t\t" . '<div class="float-right">' . $data['button'] . '</div>' . "\n";
        }
        $content .= "\t\t" . '<div class="article-title"><h1>' . $title . $data['edit_url'] . '</h1></div>' . "\n";
        if ($info) {
            $content .= "\t\t" . '<div class="article-info">' . implode($info) . "\n\t\t" . '</div>' . "\n";
        }
        $content .= "\t\t" . '<div class="article-summary">' . $data['summary'] . "\n\t\t" . '</div>' . "\n";

        // html
        $html = "\t" . '<div class="article-wrapper">' . "\n";
        if ($data['image']) {
            $data['image_html'] = '<img src="' . $data['image'] . '" alt="' . $data['title'] . '" class="responsive">';
        }
        if ($data['image_html']) {
            $html .= "\t\t" . '<div class="article-media">' . $data['image_html'] . '</div>' . "\n";
        }
        $html .= "\t\t" . '<div class="article-content">' . $content . '</div>' . "\n";
        $html .= "\t" . '</div>' . "\n";
        $html .= "\t" . '<div class="article-description">' . $data['description'] . "\n\t\t" . '</div>' . "\n";

        // tabs
        if ($this->tabs) {
            $html .= $this->tabs->render();
        }

        return $html;
    }
}
