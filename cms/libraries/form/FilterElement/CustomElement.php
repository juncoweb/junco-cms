<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Form\FilterElement;

class CustomElement extends FilterElement
{
    /**
     * Constructor
     *
     * @param string  $name
     * @param array	  $attr
     */
    public function __construct(
        protected string $name,
        string $content
    ) {
        $this->html = $content;
    }
}
