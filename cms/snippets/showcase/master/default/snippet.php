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
        'id'            => '',
        'url'            => '',
        'image'            => '',
        'image_html'    => '',
        'title'            => '',
        'description'    => '',
        'date'            => '',
        'author_name'    => '',
        'author_url'    => '',
        'edit_url'        => '',
        'button'        => '',
        'info'            => [],
        'labels'        => [],
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
            $data['body'],
            $data['url'],
            $data['title'],
            $data['intro'] ?: $data['body'],
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

        if ($data['author_name']) {
            $info[] = '<div class="article-author">' . sprintf(_t('By %s'), '<a href="' . $data['author_url'] . '">' . $data['author_name'] . '</a>') . '</div>';
        }

        if ($data['labels']) {
            $info[] = '<div class="article-labels">' . sprintf(_t('Labels %s'), implode(', ', array_map(function ($label) {
                return sprintf('<a href="%s">%s</a>', $label['url'], $label['name']);
            }, $data['labels']))) . '</div>';
        }

        if ($data['info']) {
            foreach ($data['info'] as $value) {
                $info[] = '<div>' . $value . '</div>';
            }
        }
        if ($data['url']) {
            $data['title'] = '<a href="' . $data['url'] . '">' . $data['title'] . '</a>';
        }
        if ($data['edit_url']) {
            $data['edit_url'] = ' <a href="' . $data['edit_url'] . '" class="edit" title="' . _t('Edit') . '"><i class="fa-solid fa-pencil font-small"></i></a>';
        }

        // content
        $content = '';
        if ($data['button']) {
            $content .= "\t\t" . '<div class="float-right">' . $data['button'] . '</div>' . "\n";
        }
        $content .= "\t\t" . '<div class="article-title"><h1>' . $data['title'] . $data['edit_url'] . '</h1></div>' . "\n";
        if ($info) {
            $content .= "\t\t" . '<div class="article-info">' . implode($info) . "\n\t\t" . '</div>' . "\n";
        }
        $content .= "\t\t" . '<div class="article-description">' . $data['description'] . "\n\t\t" . '</div>' . "\n";

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

        // tabs
        if ($this->tabs) {
            $html .= $this->tabs->render();
        }

        return $html;
    }
}
