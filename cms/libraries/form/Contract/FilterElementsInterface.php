<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Form\Contract;

use Junco\Form\FilterElement\Checkbox;
use Junco\Form\FilterElement\CustomSelect;
use Junco\Form\FilterElement\InputSelect;
use Junco\Form\FilterElement\SearchInput;

interface FilterElementsInterface
{
    /**
     * Set Values
     *
     * @param ?array $values
     */
    public function setValues(?array $values = null): void;

    /**
     * Load
     *
     * @param string  $plugin
     * @param array   $attr
     * 
     * @return FilterElementInterface
     */
    public function load(string $plugin, array $attr = []): FilterElementInterface;

    /**
     * Input
     *
     * @param string $name
     * @param array	 $attr
     * 
     * @return FilterElementInterface
     */
    public function input(string $name, array $attr = []): FilterElementInterface;

    /**
     * Button
     * 
     * @param array	$attr
     * 
     * @return FilterElementInterface
     */
    public function button(array $attr = []): FilterElementInterface;

    /**
     * Search
     * 
     * @return FilterElement
     */
    public function search(): SearchInput;

    /**
     * Input and selector in group.
     * 
     * @param array   $options
     * 
     * @return FilterElement
     */
    public function searchIn(array $options): InputSelect;

    /**
     * CustomSelect
     *
     * @param string  $name
     * @param array   $options
     * @param ?string $input_name	if exists, creates an input
     * 
     * @return CustomSearch
     */
    public function select(string $name = '', array $options = []): CustomSelect;

    /**
     * Checkbox
     * 
     * @param string       $name
     * @param array|string $attr
     * 
     * @return FilterElement
     */
    public function checkbox(string $name = '', array|string $attr = []): Checkbox;

    /**
     * Group
     * 
     * @param FilterElementInterface[]
     * 
     * @return FilterElementInterface
     */
    public function group(FilterElementInterface ...$elements): FilterElementInterface;

    /**
     * Hidden
     * @param string $name
     * @param string $value
     * 
     * @return void
     */
    public function hidden(string $name = '', mixed $value = null): void;
}
