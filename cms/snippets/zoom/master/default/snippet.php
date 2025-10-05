<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

class zoom_master_default_snippet extends ZoomBase
{
    /**
     * Render
     * 
     * @return string
     */
    public function render(): string
    {
        $html = '';
        foreach ($this->groups as $group) {
            if (is_array($group)) {
                $partial = '';
                foreach ($group as $column) {
                    $partial .= '<div>' . $this->renderGroup($column) . '</div>';
                }
                $html .= '<div class="form-group"><div class="form-columns">' . $partial . '</div></div>';
            } else {
                $html .= $this->renderGroup($group);
            }
        }

        return '<div class="table-responsive">' . $html . '</div>';
    }

    /**
     * Render
     */
    protected function renderGroup(ZoomGroup $group): string
    {
        $content = $group->getContent();
        $label   = $group->getLabel();
        $html    = '';

        if ($label) {
            $html .= '<div class="form-label">' . $label . '</div>';
        }

        if ($content) {
            $html .= '<div class="form-content">' . $content . '</div>';
        }

        return '<div class="form-group">' . $html . '</div>';
    }
}
