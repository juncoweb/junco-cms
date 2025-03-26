<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Form\Contract;

interface FormElementsInterface
{
    /**
     * Set Values
     *
     * @param array	$values
     * 
     * @return void
     */
    public function setValues(?array $values = null): void;

    /**
     * Set Deep
     *
     * @param string $deep_name
     * 
     * @return void
     */
    public function setDeep(string $deep_name = ''): void;

    /**
     * Loads the form elements
     *
     * @param string $plugin
     * @param array  $args
     * 
     * @return FormElementInterface
     */
    public function load(string $plugin, array $args = []): FormElementInterface;

    /**
     * Input
     *
     * @param string $name
     * @param array	 $attr
     * 
     * @return FormElementInterface
     */
    public function input(string $name, array $attr = []): FormElementInterface;

    /**
     * Button
     * 
     * @param array	$attr
     * 
     * @return FormElementInterface
     */
    public function button(array $attr = []): FormElementInterface;

    /**
     * Hidden
     *
     * @param string $name
     * @param mixed  $value
     * 
     * @return HiddenInterface
     */
    public function hidden(string $name, mixed $value = null): HiddenInterface;

    /**
     * Checkbox
     *
     * @param string $name
     * @param array  $attr
     * 
     * @return FormElementInterface
     */
    public function checkbox(string $name, array $attr = []): FormElementInterface;

    /**
     * Checkbox list
     *
     * @param string $name
     * @param array	 $options
     * @param array  $attr
     * 
     * @return FormElementInterface
     */
    public function checkboxList(string $name, array $options = [], array $attr = []): FormElementInterface;

    /**
     * Checkbox
     *
     * @param string $name
     * @param array  $attr
     */
    public function toggle(string $name, array $attr = []): FormElementInterface;

    /**
     * File
     *
     * @param string $name
     * @param array  $options
     */
    public function file(string  $name, array $options = []): FormElementInterface;

    /**
     * Enter
     *
     * @param string	$label
     * @param array		$attr
     */
    public function enter(string $label = '', array $attr = []): FormElementInterface;

    /**
     * Radio
     *
     * @param string		$name
     * @param array			$options
     * @param array			$attr
     */
    public function radio(string $name, array $options, array $attr = []): FormElementInterface;

    /**
     * Select
     *
     * @param string		$name
     * @param array			$options
     * @param array			$attr
     */
    public function select(string $name, array $options, array $attr = []): FormElementInterface;

    /**
     * Suite
     *
     * @param string		$name
     * @param array			$options
     * @param array			$attr
     */
    public function suite(string $name, array $options, array $attr = []): FormElementInterface;

    /**
     * Textarea
     *
     * @param string	$name
     * @param array		$attr
     */
    public function textarea(string $name, array $attr = []): FormElementInterface;

    /**
     * Editor
     *
     * @param string  $name
     */
    public function editor(string $name): FormElementInterface;

    /**
     * Collection
     *
     * @param string  $control - The control-felem value
     * @param string  $name
     */
    public function collection(string $control, string $name): FormElementInterface;

    /**
     * Group
     * 
     * @param FormElementInterface[]
     * 
     * @return FormElementInterface
     */
    public function group(FormElementInterface ...$elements): FormElementInterface;
}
