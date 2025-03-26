<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Form;

use Junco\Form\Contract\FilterElementInterface;
use Junco\Form\Contract\FilterElementsInterface;
use Junco\Form\FilterElement\Button;
use Junco\Form\FilterElement\Checkbox;
use Junco\Form\FilterElement\CustomElement;
use Junco\Form\FilterElement\CustomSelect;
use Junco\Form\FilterElement\InputSelect;
use Junco\Form\FilterElement\SearchInput;
use Junco\Form\FilterElement\Input;
use Junco\Form\FormElement\Hidden;
use Plugin;

abstract class FilterElements implements FilterElementsInterface
{
    // vars
    protected ?array $values    = null;
    protected array  $rows        = [];
    protected string $hidden    = '';

    /**
     * Set Values
     *
     * @param array $values
     */
    public function setValues(?array $values = null): void
    {
        $this->values = $values;
    }

    /**
     * Call
     *
     * @param string  $plugin
     * @param array   $attr
     * 
     * @return FilterElementInterface
     */
    public function load(string $plugin, array $attr = []): FilterElementInterface
    {
        $attr['value'] ??= (isset($attr['name']) && isset($this->values[$attr['name']])
            ? $this->values[$attr['name']]
            : null);

        return $this->addElement(Plugin::get('filter_element', 'load', $plugin)?->run($attr));
    }

    /**
     * Input
     *
     * @param string $name
     * @param array	 $attr
     * 
     * @return FilterElementInterface
     */
    public function input(string $name, array $attr = []): FilterElementInterface
    {
        return $this->addElement(new Input($name, $this->values[$name] ?? '', $attr));
    }

    /**
     * Button
     * 
     * @param array	$attr
     * 
     * @return FilterElementInterface
     */
    public function button(array $attr = []): FilterElementInterface
    {
        return $this->addElement(new Button($attr));
    }

    /**
     * Search
     * 
     * @return FilterElement
     */
    public function search(): SearchInput
    {
        return $this->addElement(new SearchInput('search', $this->values['search'] ?? ''));
    }

    /**
     * Input and selector in group.
     * 
     * @param array $options
     * 
     * @return FilterElement
     */
    public function searchIn(array $options): InputSelect
    {
        return $this->addElement(new InputSelect(
            'search',
            $this->values['search'] ?? null,
            'field',
            $options,
            $this->values['field'] ?? null
        ));
    }

    /**
     * CustomSelect
     *
     * @param string  $name
     * @param array   $options
     * 
     * @return CustomSelect
     */
    public function select(string $name = '', array $options = []): CustomSelect
    {
        return $this->addElement(new CustomSelect(
            $name,
            $options,
            $this->values[$name] ?? ''
        ));
    }

    /**
     * Checkbox
     * 
     * @param string       $name
     * @param array|string $attr
     * 
     * @return FilterElement
     */
    public function checkbox(string $name = '', array|string $attr = []): Checkbox
    {
        if (!is_array($attr)) {
            $attr = ['label' => $attr];
        }

        return $this->addElement(new Checkbox($name, $this->values[$name] ?? false, $attr));
    }

    /**
     * Group
     * 
     * @param FilterElementInterface[]
     * 
     * @return FilterElementInterface
     */
    public function group(FilterElementInterface ...$elements): FilterElementInterface
    {
        $html = '';
        foreach ($elements as $element) {
            array_pop($this->rows);
            $html .= $element;
        }

        return $this->addElement(new CustomElement('', '<div class="input-group">' . $html . '</div>'));
    }

    /**
     * 
     */
    protected function addElement(FilterElementInterface $element): FilterElementInterface
    {
        return $this->rows[] = $element;
    }

    /**
     * Hidden
     */
    public function hidden(string $name = '', mixed $value = null): void
    {
        $this->hidden .= new Hidden($name, $value ?? $this->values[$name] ?? null);
    }
}
