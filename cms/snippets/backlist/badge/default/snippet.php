<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

class badge_backlist_default_snippet
{
    protected array $replaces;
    protected array $badges = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->replaces = [
            ['{{ color }}' => 'default', '{{ value }}' => _t('No')],
            ['{{ color }}' => 'primary', '{{ value }}' => _t('Yes')],
        ];
    }

    /**
     * Set
     * 
     * @return void
     */
    public function setList(array $badges): void
    {
        $title = '%2$s: {{ value }}';
        $tag = '<abbr'
            .  ' class="badge badge-{{ color }} badge-regular badge-small"'
            .  ' title="' . $title . '"'
            .  ' aria-label="' . $title . '"'
            . '><span aria-hidden="true">%3$s</span></abbr>';

        $this->badges = [];
        foreach ($badges as $name => $title) {
            $this->badges[$name] = sprintf($tag, $name, $title, $title[0]);
        }
    }

    /**
     * Render
     * 
     * @param array $data
     * 
     * @return string
     */
    public function render(array $data): string
    {
        $html = '';

        foreach ($this->badges as $name => $badge) {
            $status = empty($data[$name]) ? 0 : 1;
            $html .= strtr($badge, $this->replaces[$status]);
        }

        return $html;
    }
}
