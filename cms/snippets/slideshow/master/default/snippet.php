<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

class slideshow_master_default_snippet
{
    // vars
    protected string $type = 'slide';

    /**
     * Set
     */
    public function setType(string $type)
    {
        $this->type = $type;
    }

    /**
     * Render
     */
    public function render($images)
    {
        $html = '<div class="slideshow ' . $this->type . '"><ul>';
        foreach ($images as $image) {
            $html .= '<li><div><img src="' . $image . '" /></div></li>';
        }
        $html .= '</ul></div>';

        return $html;
    }
}
