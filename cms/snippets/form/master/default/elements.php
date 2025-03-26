<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Form\FormElement\Button;
use Junco\Form\FormElement\Checkbox;
use Junco\Form\FormElement\CheckboxList;
use Junco\Form\FormElement\Collection;
use Junco\Form\FormElement\Editor;
use Junco\Form\FormElement\Enter;
use Junco\Form\FormElement\File;
use Junco\Form\FormElement\Hidden;
use Junco\Form\FormElement\Input;
use Junco\Form\FormElement\Radio;
use Junco\Form\FormElement\Select;
use Junco\Form\FormElement\Suite;
use Junco\Form\FormElement\Textarea;
use Junco\Form\FormElement\Toggle;
use Junco\Form\Contract\FormElementInterface;
use Junco\Form\Contract\HiddenInterface;
use Junco\Form\Contract\FormElementsInterface;
use Junco\Form\FormElement\CustomElement;

class form_master_default_elements implements FormElementsInterface
{
    // vars
    protected ?array $values    = null;
    protected string $deep_name    = '';
    protected array  $rows        = [];

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
     * Set Deep
     *
     * @param string $deep
     */
    public function setDeep(string $deep_name = ''): void
    {
        $this->deep_name = $deep_name;
    }

    /**
     * Call
     *
     * @param string  $plugin
     * @param array   $attr
     * 
     * @return FormElementInterface
     */
    public function load(string $plugin, array $attr = []): FormElementInterface
    {
        $attr['value'] ??= (isset($attr['name']) && isset($this->values[$attr['name']])
            ? $this->values[$attr['name']]
            : null);

        if (isset($attr['name'])) {
            $attr['name'] .= $this->deep_name;
        }

        return $this->addElement(Plugin::get('form_element', 'load', $plugin)?->run($attr));
    }

    /**
     * Input
     *
     * @param string $name
     * @param array	 $attr
     * 
     * @return FormElementInterface
     */
    public function input(string $name, array $attr = []): FormElementInterface
    {
        return $this->addElement(new Input($name . $this->deep_name, $this->values[$name] ?? '', $attr));
    }

    /**
     * Button
     * 
     * @param array	$attr
     * 
     * @return FormElementInterface
     */
    public function button(array $attr = []): FormElementInterface
    {
        if (isset($attr['name'])) {
            $attr['name'] .= $this->deep_name;
        }

        return $this->addElement(new Button($attr));
    }

    /**
     * Hidden
     *
     * @param string $name
     * @param mixed  $value
     * 
     * @return HiddenInterface
     */
    public function hidden(string $name, mixed $value = null): HiddenInterface
    {
        return $this->addHidden(new Hidden($name . $this->deep_name, $value ?? $this->values[$name] ?? null));
    }

    /**
     * Checkbox
     *
     * @param string $name
     * @param string $label
     * @param array  $attr
     * 
     * @return FormElementInterface
     */
    public function checkbox(string $name, array $attr = []): FormElementInterface
    {
        return $this->addElement(new Checkbox($name . $this->deep_name, $this->values[$name] ?? null, $attr));
    }

    /**
     * Checkbox list
     *
     * @param string $name
     * @param array	 $options
     * @param array  $attr
     * 
     * @return FormElementInterface
     */
    public function checkboxList(string $name, array $options = [], array $attr = []): FormElementInterface
    {
        return $this->addElement(new CheckboxList($name . $this->deep_name, $options, $this->values[$name] ?? [], $attr));
    }

    /**
     * Checkbox
     *
     * @param string $name
     * @param array  $attr
     */
    public function toggle(string $name, array $attr = []): FormElementInterface
    {
        return $this->addElement(new Toggle($name . $this->deep_name, $this->values[$name] ?? '', $attr));
    }

    /**
     * File
     *
     * @param string $name
     * @param array  $options
     */
    public function file(string  $name, array $options = []): FormElementInterface
    {
        return $this->addElement(new File($name . $this->deep_name, $options));
    }

    /**
     * Enter
     *
     * @param string	$label
     * @param array		$attr
     */
    public function enter(string $label = '', array $attr = []): FormElementInterface
    {
        return $this->addElement(new Enter($label, $attr));
    }

    /**
     * Radio
     *
     * @param string $name
     * @param array	 $options
     * @param array  $attr
     */
    public function radio(string $name, array $options, array $attr = []): FormElementInterface
    {
        return $this->addElement(new Radio($name . $this->deep_name, $this->values[$name] ?? '', $options, $attr));
    }

    /**
     * Select
     *
     * @param string $name
     * @param array  $options
     * @param array  $attr
     */
    public function select(
        string  $name,
        array   $options,
        array   $attr = []
    ): FormElementInterface {
        return $this->addElement(new Select($name . $this->deep_name, $this->values[$name] ?? '', $options, $attr));
    }

    /**
     * Suite
     *
     * @param string $name
     * @param array  $options
     * @param array  $attr
     */
    public function suite(
        string  $name,
        array   $options,
        array   $attr = []
    ): FormElementInterface {
        return $this->addElement(new Suite($name . $this->deep_name, $this->values[$name] ?? [], $options, $attr));
    }

    /**
     * Textarea
     *
     * @param string $name
     * @param array  $attr
     */
    public function textarea(string $name, array $attr = []): FormElementInterface
    {
        return $this->addElement(new Textarea($name . $this->deep_name, $this->values[$name] ?? '', $attr));
    }

    /**
     * Editor
     *
     * @param string $name
     */
    public function editor(string $name): FormElementInterface
    {
        return $this->addElement(new Editor($name . $this->deep_name, $this->values[$name] ?? ''));
    }

    /**
     * Collection
     *
     * @param string $control - The control-felem value
     * @param string $name
     */
    public function collection(string $control, string $name): FormElementInterface
    {
        return $this->addElement(new Collection($control, $name . $this->deep_name, $this->values[$name] ?? '', $this->values['__' . $name] ?? ''));
    }

    /**
     * Group
     * 
     * @param FormElementInterface[]
     * 
     * @return FormElementInterface
     */
    public function group(FormElementInterface ...$elements): FormElementInterface
    {
        $content    = '';
        $label        = null;
        $required    = '';
        $help        = '';

        foreach ($elements as $element) {
            array_pop($this->rows);

            $html = $element->render();

            if ($element::class === 'Junco\Form\FormElement\Checkbox') {
                $html = '<div class="btn input-auto">' . $html . '</div>';
            }

            $content .= $html;

            if ($label === null) {
                $label = $element->getLabel();
            }

            if (!$required && $element->isRequired()) {
                $required = true;
            }

            if (!$help) {
                $help = $element->getHelp();
            }
        }

        $element = new CustomElement('', '<div class="input-group">' . $content . '</div>');

        if ($label !== null) {
            $element->setLabel($label);
        }

        if ($required) {
            $element->setRequired();
        }

        if ($help) {
            $element->setHelp($help);
        }

        return $this->addElement($element);
    }

    /**
     * 
     */
    protected function addElement(FormElementInterface $element): FormElementInterface
    {
        return $this->rows[] = $element;
    }

    /**
     * 
     */
    protected function addHidden(HiddenInterface $element): HiddenInterface
    {
        return $element;
    }
}
