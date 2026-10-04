<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

defined('PAGINATION_PAGE') or
    define('PAGINATION_PAGE', 'page');

class Pagination
{
    // vars
    public int $cur_page      = 0;
    public int $rows_per_page = 15;
    public int $num_rows      = 0;
    public int $num_pages     = 1;
    public int $offset        = 0;
    protected string $snippet = '';
    protected array  $rows    = [];

    // navigation
    public string $nav_href = 'javascript:void(0)';

    /**
     * Performs the paging of an array
     * 
     * @param array $rows
     * 
     * @return void
     */
    public function slice(array $rows): void
    {
        $this->num_rows = count($rows);
        $this->calculate();
        $this->rows = array_slice($rows, $this->offset, $this->rows_per_page);
    }

    /**
     * Calculate
     * 
     * @return void
     */
    public function calculate(): void
    {
        if (!$this->cur_page) {
            $this->cur_page = Filter::input(GET, PAGINATION_PAGE, 'id')
                ?: Filter::input(POST, PAGINATION_PAGE, 'id')
                ?: 1;
        }
        if ($this->cur_page < 1) {
            $this->cur_page = 1;
        }

        $this->num_pages = ceil($this->num_rows / $this->rows_per_page);
        $this->cur_page  = $this->num_pages > 1
            ? ($this->cur_page < $this->num_pages ? $this->cur_page : $this->num_pages)
            : 1;
        $this->offset = ($this->cur_page - 1) * $this->rows_per_page;
    }

    /**
     * Set
     * 
     * @param array $rows
     * 
     * @return void
     */
    public function setRows(array $rows): void
    {
        $this->rows = $rows;
    }

    /**
     * Fetch all results
     * 
     * @return array
     */
    public function fetchAll(): array
    {
        return $this->rows;
    }

    /**
     * Build the url by adding the page number
     * 
     * @param string $route
     * @param array  $args
     * @param string $hash
     * 
     * @param ?string $route
     * 
     */
    public function url(string $route = '', array $args = [], string $hash = ''): void
    {
        if ($args) {
            $args = array_filter($args);
        }
        $args[PAGINATION_PAGE] = '{{ page }}';
        $this->nav_href = url($route, $args) . $hash;
    }

    /**
     * Build
     *
     * @build the html of the page.
     *
     * @param array  $tags  
     * @param array  $arrows  
     * @param int    $num_links
     * 
     * @return array
     */
    public function build(
        array   $tags,
        array   $arrows,
        int     $num_links = -1,
        ?string $extremes = null
    ): array {
        $data = [];

        if ($arrows) { // nav arrows
            if ($this->cur_page != 1) {
                $data['first'] = 1;
                $data['prev']  = $this->cur_page - 1;
            } else {
                $data['first'] =
                    $data['prev'] = 0;
            }

            if ($this->cur_page < $this->num_pages) {
                $data['next'] = $this->cur_page + 1;
                $data['last'] = $this->num_pages;
            } else {
                $data['next'] =
                    $data['last'] = 0;
            }

            foreach ($arrows as $key => $arrow) {
                $title = match ($key) {
                    'prev'  => _t('Previous page'),
                    'next'  => _t('Next page'),
                    'first' => _t('First page'),
                    'last'  => _t('Last page'),
                };
                $placeholder = '<span aria-hidden="true">' . $arrow . '</span><span class="visually-hidden">' . $title . '</span>';
                if ($data[$key]) {
                    $data[$key] = strtr($tags[0], [
                        '{{ page }}'        => $data[$key],
                        '{{ placeholder }}' => $placeholder,
                        '{{ title }}'       => $title
                    ]);
                } else {
                    $data[$key] = strtr($tags[1], [
                        '{{ placeholder }}' => $placeholder,
                        '{{ title }}'       => $title
                    ]);
                }
            }
        }

        if ($num_links > -1) { // vars
            $from = $this->cur_page - $num_links;
            $to   = $this->cur_page + $num_links;
            $html = '';

            if ($from < 1) {
                $to  -= $from - 1;
                $from = 1;
            }

            if ($to > $this->num_pages) {
                $from -= $to - $this->num_pages;
                $to    = $this->num_pages;

                if ($from < 1) {
                    $from = 1;
                }
            }

            for ($i = $from; $i <= $to; $i++) {
                $title = sprintf($t ??= _t('Page %d'), $i);
                $placeholder = '<span aria-hidden="true">' . $i . '</span><span class="visually-hidden">' . $title . '</span>';

                if ($i != $this->cur_page) {
                    $html .= strtr($tags[0], [
                        '{{ page }}'        => $i,
                        '{{ placeholder }}' => $placeholder,
                        '{{ title }}'       => $title
                    ]);
                } else {
                    $html .= strtr($tags[2], [
                        '{{ placeholder }}' => $placeholder,
                        '{{ title }}'       => $title
                    ]);
                }
            }

            //
            $data['from']       = $from;
            $data['to']         = $to;
            $data['numeration'] = $html;

            if ($extremes !== null) { // build: 1 ...   ... 99
                if ($from != 1) {
                    $page = 1;
                    $title = _t('First page');
                    $placeholder = '<span aria-hidden="true">' . $page . '</span><span class="visually-hidden">' . $title . '</span>';

                    $data['first_number'] = strtr($tags[0], [
                        '{{ page }}'        => $page,
                        '{{ placeholder }}' => $placeholder,
                        '{{ title }}'       => $title
                    ]) . $extremes;
                } else {
                    $data['first_number'] = '';
                }

                if ($to != $this->num_pages) {
                    $page = $this->num_pages;
                    $title = _t('Last page');
                    $placeholder = '<span aria-hidden="true">' . $page . '</span><span class="visually-hidden">' . $title . '</span>';

                    $data['last_number'] = $extremes . strtr($tags[0], [
                        '{{ page }}'        => $page,
                        '{{ placeholder }}' => $placeholder,
                        '{{ title }}'       => $title
                    ]);
                } else {
                    $data['last_number'] = '';
                }
            }
        }

        return $data;
    }

    /**
     * Sets the snippet to use
     * 
     * @param string $snippet
     * 
     * @return void
     */
    public function snippet(string $snippet = ''): void
    {
        $this->snippet = $snippet;
    }

    /**
     * Render
     * 
     * @return string
     */
    public function render(): string
    {
        return snippet('pagination', $this->snippet)->render($this);
    }

    /**
     * To string representation
     * 
     * @return string
     */
    public function __toString(): string
    {
        return $this->render();
    }
}
