<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

class SearchCollector
{
    protected $rows = [];

    /**
     * Get
     */
    public function set(string $title = ''): SearchResults
    {
        $row = [
            'title' => $title,
            'results' => [],
        ];

        $this->rows[] = &$row;
        return new SearchResults($row['results']);
    }

    /**
     * Set
     */
    public function get(): array
    {
        return $this->rows;
    }
}

/**
 * Results
 */
class SearchResults
{
    /**
     * Constructor
     */
    public function __construct(protected array &$results) {}

    /**
     * add
     */
    public function add(string $header = '', string $url = ''): void
    {
        $this->results[] = [
            'header' => $header,
            'url'    => $url,
        ];
    }
}
