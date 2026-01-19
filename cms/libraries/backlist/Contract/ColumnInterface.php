<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Backlist\Contract;

use Junco\Backlist\Contract\FiltersInterface;

interface ColumnInterface
{
    /**
     * Before
     */
    public function setBefore(string $before): static;

    /**
     * After
     */
    public function setAfter(string $after): static;

    /**
     * Label
     */
    public function setLabel(string $label, ?FiltersInterface $filters = null): static;

    /**
     * Label
     */
    public function setLabelIcon(string $icon, string $title = '', ?FiltersInterface $filters = null): static;

    /**
     * Width
     */
    public function setWidth(string $width = ''): static;

    /**
     * Class
     */
    public function setClass(string $className): static;

    /**
     * Class
     */
    public function setSubtle(): static;

    /**
     * Align left
     */
    public function alignCenter(): static;

    /**
     * Align Right
     */
    public function alignRight(): static;

    /**
     * No wrap
     */
    public function noWrap(): static;

    /**
     * Linked
     */
    public function setLinked(): static;

    /**
     * Keep
     */
    public function keep(string $keep_name): static;
}
