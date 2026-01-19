<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Backlist\Contract;

interface ControlInterface extends ColumnInterface
{
    /**
     * Set
     * 
     * @param string $text
     * @param string $title
     * 
     * @return static
     */
    public function setText(string $text, string $title = ''): static;

    /**
     * Set
     * 
     * @param string $icon
     * @param string $title
     * 
     * @return static
     */
    public function setIcon(string $icon, string $title = ''): static;

    /**
     * Set
     * 
     * @param string $control
     */
    public function setAttr(array $attr): static;
}
