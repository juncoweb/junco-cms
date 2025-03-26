<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */


class EmailMessageCollector
{
    // vars
    private $sections = [];

    /**
     * Section
     */
    public function section(string $title = '')
    {
        $row = [
            'title' => $title,
            'articles' => [],
        ];

        $this->sections[] = &$row;
        return new EmailArticles($row['articles']);
    }

    /**
     * Get
     */
    public function get()
    {
        return $this->sections;
    }
}

class EmailArticles
{
    public $articles = null;

    /**
     * Constructor
     */
    public function __construct(&$articles)
    {
        $this->articles = &$articles;
    }

    /**
     * Add Article
     */
    public function add(
        string $title = '',
        string $meta = '',
        string $content = '',
        string $image = '',
        string $url = ''
    ) {
        $this->articles[] = [
            'title'        => $title,
            'meta'        => $meta,
            'content'    => $content,
            'image'        => $image,
            'url'        => $url,
        ];
    }
}
