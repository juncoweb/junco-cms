<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Form\Contract;

interface FormInterface extends FormElementsInterface
{
    /**
     * Actions
     * 
     * @param string $snippet
     */
    public function getActions(string $snippet = ''): FormActionsInterface;

    /**
     * Header
     * 
     * @param mixed $attr
     * @param ?bool $toggle
     */
    public function header(mixed $attr = null, ?bool $toggle = null): void;

    /**
     * Separate
     * 
     * @param array|string|null $attr
     * 
     * @return void
     */
    public function separate(array|string|null $attr = null): void;

    /**
     * Columns
     * 
     * @param FormElementInterface[]
     * 
     * @return void
     */
    public function columns(FormElementInterface ...$elements): void;

    /**
     * Adds a block
     * 
     * @param string $html
     * 
     * @return void
     */
    public function addBlock(string $html): void;

    /**
     * Get the las element
     * 
     * @return FormElementInterface
     */
    public function getLastElement(): FormElementInterface;

    /**
     * Render
     * 
     * @return string
     */
    public function renderForm(string $html, string|int $form_id = ''): string;

    /**
     * Render
     * 
     * @return string
     */
    public function render(): string;
}
