<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Responder\Contract;

interface AjaxJsonInterface extends ResponderInterface
{
    /**
     * Sets the json content.
     * 
     * @param array $content
     * 
     * @return void
     */
    public function setContent(array $content): void;
}
