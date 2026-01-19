<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Showcase;

use Junco\Form\FormElement\Button;
use Junco\Showcase\Contract\ShowcaseInterface;
use Junco\Tabs\TabsInterface;

abstract class ShowcaseBase implements ShowcaseInterface
{
    // vars
    protected ?TabsInterface $tabs = null;
    protected string $tabs_snippet = '';
    protected array  $data         = [];
    //
    private   array  $options      = [];
    private   bool   $assets       = false;

    /**
     * Sets the internal options.
     * 
     * @param array $options
     * 
     * @return void
     */
    public function setOptions(array $options): void
    {
        $this->options = array_merge($this->options, $options);
    }

    /**
     * Get the option or default value.
     * 
     * @param string $name
     * @param mixed  $default
     * 
     * @return mixed
     */
    public function getOption(string $name, mixed $default = null): mixed
    {
        return $this->options[$name] ?? $default;
    }

    /**
     * Load own assets.
     * 
     * @param bool $status
     * 
     * @return void
     */
    public function setAssets(bool $status = true): void
    {
        $this->assets = $status;
    }

    /**
     * Assets
     * 
     * @param array $options
     * 
     * @return void
     */
    protected function assets(array $options): void
    {
        if ($this->assets && $options) {
            app('assets')->options($options);
        };
    }

    /**
     * Main
     * 
     * @deprecated
     * 
     * @param array $data
     */
    public function main(array $data): void
    {
        $this->data = array_merge($this->data, $data);
    }

    /**
     * Image
     * 
     * @param string $image
     */
    public function image(string $image, bool $is_html = false): void
    {
        if ($is_html) {
            $this->data['image_html'] = $image;
        } else {
            $this->data['image'] = $image;
        }
    }

    /**
     * Title
     * 
     * @param string $title
     */
    public function title(string $title): void
    {
        $this->data['title'] = $title;
    }

    /**
     * Url
     * 
     * @param string $url
     */
    public function url(string $url): void
    {
        $this->data['url'] = $url;
    }

    /**
     * Url
     * 
     * @param string $url
     */
    public function editUrl(string $url): void
    {
        $this->data['edit_url'] = $url;
    }

    /**
     * Date
     * 
     * @param string $date
     */
    public function date(string $date): void
    {
        $this->data['date'] = $date;
    }

    /**
     * Price
     * 
     * @param string $price
     * @param string $discount
     */
    public function price(string $price, string $discount = ''): void
    {
        $this->data['price']    = $price;
        $this->data['discount'] = $discount;
    }

    /**
     * Rating
     * 
     * @param float $avg
     */
    public function rating(float $avg): void
    {
        $this->data['avg_ratings'] = $avg;
    }

    /**
     * Visits
     * 
     * @param int $total
     */
    public function numVisits(int $total): void
    {
        $this->data['num_visits'] = $total;
    }

    /**
     * Comments
     * 
     * @param int $total
     */
    public function numComments(int $total): void
    {
        $this->data['num_comments'] = $total;
    }

    /**
     * Info
     * 
     * @param string $content
     * @param string $title
     * @param string $icon
     */
    public function info(string $content, string $title = '', string $icon = ''): void
    {
        if ($title) {
            $content = '<span class="visually-hidden">' . $title . '</span>' . $content;
        }

        if ($icon) {
            $content = '<i class="' . $icon . '"' . ($title ? ' title="' . $title . '"' : '') . ' aria-hidden="true"></i> ' . $content;
        }

        $this->data['info'][] = $content;
    }

    /**
     * Author
     * 
     * @param string $name
     * @param string $url
     * @param int    $id
     */
    public function author(string $name, string $url = '', int $id = 0): void
    {
        $this->data['authors'][] = [
            'name' => $name,
            'url'  => $url,
            'id'   => $id,
        ];
    }

    /**
     * Labels
     * 
     * @param array $labels
     */
    public function labels(array $labels): void
    {
        $this->data['labels'] = $labels;
    }

    /**
     * Button
     *
     * @param array $attr
     */
    public function button(array $attr = []): void
    {
        $this->data['button'] = new Button($attr);
    }

    /**
     * Summary
     * 
     * @param string $summary
     */
    public function summary(?string $summary): void
    {
        if ($summary) {
            $this->data['summary'] = $summary;
        }
    }

    /**
     * Summary
     * 
     * @param string $description
     */
    public function description(?string $description): void
    {
        if ($description) {
            $this->data['description'] = $description;
        }
    }

    /**
     * Tabs
     * 
     * @param string $snippet
     * @param string $id
     * @param array  $options
     * 
     * @return TabsInterface
     */
    public function getTabs(string $snippet = '', string $id = '', array $options = []): TabsInterface
    {
        if (empty($options['class'])) {
            $options['class'] = 'responsive';
        } else {
            $options['class'] .= ' responsive';
        }

        return $this->tabs = snippet('tabs', $snippet ?: $this->tabs_snippet, $id, $options);
    }

    /**
     * 
     */
    protected function renderLinks(array $links, string $separator = ', '): string
    {
        return implode($separator, array_map(function ($link) {
            return sprintf('<a href="%s">%s</a>', $link['url'], $link['name']);
        }, $links));
    }
}
