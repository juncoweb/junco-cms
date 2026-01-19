<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Samples\Snippet;

interface SampleInterface
{
    /**
     * Constructor
     */
    public function __construct(string $code = '');

    /**
     * Label
     * 
     * @param string $label
     * 
     * @return static
     */
    public function setLabel(string $label): static;

    /**
     * Context
     * 
     * @param string $context
     * 
     * @return static
     */
    public function setContext(string $context): static;

    /**
     * Render
     * 
     * @return string
     */
    public function render(): string;
}
