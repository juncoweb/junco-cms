<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Form\Contract;

interface FormElementInterface
{
    /**
     * Set
     * 
     * @param ?string $label
     * 
     * @return self
     */
    public function setLabel(?string $label = ''): self;

    /**
     * Get
     * 
     * @return ?string
     */
    public function getLabel(): ?string;

    /**
     * Set
     * 
     * @param bool $required
     * 
     * @return self
     */
    public function setRequired(bool $required = true): self;

    /**
     * Return required status
     * 
     * @return bool
     */
    public function isRequired(): bool;

    /**
     * Set
     * 
     * @param string $message
     * 
     * @return self
     */
    public function setHelp(string $message): self;

    /**
     * Get
     * 
     * @return string
     */
    public function getHelp(): string;

    /**
     * Set
     * 
     * @param array $attr
     * 
     * @return self
     */
    public function setAction(array $attr): self;

    /**
     * Get
     * 
     * @return string
     */
    public function render(): string;

    /**
     * To string representation.
     * 
     * @return string
     */
    public function __toString(): string;
}
