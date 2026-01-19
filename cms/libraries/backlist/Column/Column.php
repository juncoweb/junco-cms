<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Backlist\Column;

use Junco\Backlist\Contract\ColumnInterface;
use Junco\Backlist\Contract\FiltersInterface;

class Column extends ColumnBase implements ColumnInterface
{
    /**
     * Constructor
     * 
     * @param string $name
     */
    public function __construct(string $column)
    {
        if ($column) {
            $this->td = $this->normalize($column);
        }

        return $this;
    }

    /**
     * Before
     */
    public function setBefore(string $before): static
    {
        if ($before) {
            $this->td_before = $this->normalize($before);
        }

        return $this;
    }

    /**
     * After
     */
    public function setAfter(string $after): static
    {
        if ($after) {
            $this->td_after = $this->normalize($after);
        }

        return $this;
    }

    /**
     * Label
     */
    public function setLabel(string $label, ?FiltersInterface $filters = null): static
    {
        if ($filters) {
            $label = $filters->sort_h($label);
        }

        $this->th = $label;

        return $this;
    }

    /**
     * Label
     */
    public function setLabelIcon(string $icon, string $title = '', ?FiltersInterface $filters = null): static
    {
        $this->th_class .= ' text-center';
        $this->td_class .= ' text-center';
        $this->width = 20;

        if ($title) {
            $label = '<span class="visually-hidden">' . $title . '</span>';
            $label .= '<i class="' . $icon . '" title="' . $title . '" aria-hidden="true"></i>';
        } else {
            $label = '<i class="' . $icon . '" aria-hidden="true"></i>';
        }
        $this->setLabel($label, $filters);

        return $this;
    }

    /**
     * Width
     */
    public function setWidth(string $width = ''): static
    {
        $this->width = $width;
        return $this;
    }

    /**
     * Class
     */
    public function setClass(string $className): static
    {
        $this->td_class .= ' ' . $className;
        return $this;
    }

    /**
     * Class
     */
    public function setSubtle(): static
    {
        $this->td_class .= ' table-subtle-color';
        return $this;
    }

    /**
     * Align left
     */
    public function alignCenter(): static
    {
        $this->th_class .= ' text-center';
        $this->td_class .= ' text-center';
        return $this;
    }

    /**
     * Align Right
     */
    public function alignRight(): static
    {
        $this->th_class .= ' text-right';
        $this->td_class .= ' text-right';
        return $this;
    }

    /**
     * No wrap
     */
    public function noWrap(): static
    {
        $this->th_class .= ' text-nowrap';
        $this->td_class .= ' text-nowrap';
        return $this;
    }

    /**
     * Linked
     */
    public function setLinked(): static
    {
        $this->td_class .= ' table-linked';
        return $this;
    }

    /**
     * Set
     */
    public function keep(string $keep_name): static
    {
        $this->keep = $keep_name;
        return $this;
    }
}
