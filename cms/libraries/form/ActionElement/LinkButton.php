<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Form\ActionElement;

class LinkButton extends ActionElement
{
    /**
     * Constructor
     *
     * @param string	$href
     * @param string 	$title
     * @param string 	$caption
     */
    public function __construct(string $href = '', string $title = '', array $attr = '')
    {
        if ($icon) {
            $caption = '<i class="' . $icon . '" aria-hidden="true"></i>';
            $caption .= sprintf($this->title_tag, $title);
        } else {
            $caption = $title;
        }

        $this->html = '<a href="' . $href . '" class="btn" data-tooltip title="' . $title . '">'
            . $caption
            . '</a>';
    }
}
