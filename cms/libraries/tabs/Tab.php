<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Tabs;

class Tab
{
    protected string $label      = '';
    protected string $panel      = '';
    protected string $icon       = '';
    protected bool   $responsive = false;

    /**
     * Constructor
     * 
     * @param string $label
     * @param string $panel
     */
    public function __construct(string $label, string $panel)
    {
        $this->label = $label;
        $this->panel = $panel;
    }

    /**
     * Set the icon for the tab
     * 
     * @param string $icon
     * @param bool $responsive
     * 
     * @return self
     */
    public function setIcon(string $icon, bool $responsive = false): self
    {
        $this->icon       = $icon;
        $this->responsive = $responsive;
        return $this;
    }

    /**
     * To array
     * 
     * @return array
     */
    public function toArray(): array
    {
        return [
            'label' => $this->label,
            'panel' => $this->panel,
            'icon' => $this->icon,
            'responsive' => $this->responsive
        ];
    }
}
