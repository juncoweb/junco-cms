<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Form\FilterElement;

class CustomElement extends FilterElement
{
    /**
     * Constructor
     *
     * @param string $name
     * @param string $content
     */
    public function __construct(
        protected string $name,
        string $content
    ) {
        $this->html = $content;
    }
}
