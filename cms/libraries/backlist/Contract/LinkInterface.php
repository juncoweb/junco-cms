<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Backlist\Contract;

interface LinkInterface extends ColumnInterface
{
    /**
     * Set
     * 
     * @param string $text
     * 
     * @return static
     */
    public function setText(string $text): static;

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
     * 
     * @return static
     */
    public function setAttr(array $attr): static;

    /**
     * Target
     * 
     * @return static
     */
    public function setTarget(string $target = '_blank'): static;
}
