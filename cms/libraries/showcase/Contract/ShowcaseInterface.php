<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Showcase\Contract;

use Junco\Tabs\TabsInterface;

interface ShowcaseInterface
{
    /**
     * Sets the internal options.
     * 
     * @param array $options
     * 
     * @return void
     */
    public function setOptions(array $options): void;

    /**
     * Get the option or default value.
     * 
     * @param string $name
     * @param mixed  $default
     * 
     * @return mixed
     */
    public function getOption(string $name, mixed $default = null): mixed;

    /**
     * Load own assets.
     * 
     * @param bool $status
     * 
     * @return void
     */
    public function setAssets(bool $status = true): void;

    /**
     * Main
     * 
     * @deprecated
     * 
     * @param array $data
     */
    public function main(array $data): void;

    /**
     * Image
     * 
     * @param string $image
     */
    public function image(string $image, bool $is_html = false): void;

    /**
     * Title
     * 
     * @param string $title
     */
    public function title(string $title): void;

    /**
     * Url
     * 
     * @param string $url
     */
    public function url(string $url): void;

    /**
     * Url
     * 
     * @param string $url
     */
    public function editUrl(string $url): void;

    /**
     * Date
     * 
     * @param string $date
     */
    public function date(string $date): void;

    /**
     * Price
     * 
     * @param string $price
     * @param string $discount
     */
    public function price(string $price, string $discount = ''): void;

    /**
     * Rating
     * 
     * @param float $avg
     */
    public function rating(float $avg): void;

    /**
     * Visits
     * 
     * @param int $total
     */
    public function numVisits(int $total): void;

    /**
     * Comments
     * 
     * @param int $total
     */
    public function numComments(int $total): void;

    /**
     * Info
     * 
     * @param string $content
     * @param string $title
     * @param string $icon
     */
    public function info(string $content, string $title = '', string $icon = ''): void;

    /**
     * Author
     * 
     * @param string $name
     * @param string $url
     * @param int    $id
     */
    public function author(string $name, string $url = '', int $id = 0): void;

    /**
     * Labels
     * 
     * @param array $labels
     */
    public function labels(array $labels): void;

    /**
     * Button
     *
     * @param array $attr
     */
    public function button(array $attr = []): void;

    /**
     * Summary
     * 
     * @param string $summary
     */
    public function summary(?string $summary): void;
    /**
     * Summary
     * 
     * @param string $description
     */
    public function description(?string $description): void;

    /**
     * Tabs
     * 
     * @param string $snippet
     * @param string $id
     * @param array  $options
     * 
     * @return TabsInterface
     */
    public function getTabs(string $snippet = '', string $id = '', array $options = []): TabsInterface;

    /**
     * Render
     * 
     * @return string
     */
    public function render(): string;
}
