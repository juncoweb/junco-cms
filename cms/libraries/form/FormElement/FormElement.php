<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Form\FormElement;

use Junco\Form\Contract\FormElementInterface;

abstract class FormElement implements FormElementInterface
{
    // vars
    protected string  $name     = '';
    protected string  $content  = '';
    protected ?string $label    = null;
    protected bool    $required = false;
    protected string  $help     = '';

    /**
     * Set
     * 
     * @param ?string $label
     * 
     * @return self
     */
    public function setLabel(?string $label = ''): self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get
     * 
     * @return string
     */
    public function getLabel(): ?string
    {
        if ($this->label) {
            if ($this->name) {
                return '<label for="' . $this->name . '" class="input-label">' . $this->label . '</label>';
            }

            return '<label class="input-label">' . $this->label . '</label>';
        }

        return $this->label;
    }

    /**
     * Set
     * 
     * @param bool $required
     * 
     * @return self
     */
    public function setRequired(bool $required = true): self
    {
        $this->required = $required;

        return $this;
    }

    /**
     * Required
     * 
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->required;
    }

    /**
     * Set
     * 
     * @param string $message
     * 
     * @return self
     */
    public function setHelp(string $message): self
    {
        $this->help = $message;

        return $this;
    }

    /**
     * Get
     * 
     * @return string
     */
    public function getHelp(): string
    {
        if ($this->help && $this->name) {
            return '<span aria-describedby="' . $this->name . '">' . $this->help . '</span>';
        }

        return $this->help;
    }

    /**
     * Set
     * 
     * @param array $attr
     * 
     * @return self
     */
    public function setAction(array $attr): self
    {
        if (strpos($this->content, '<div class="input-actions">') !== false) {
            $this->content = substr($this->content, 0, -6)
                . '<div>' . $this->action($attr) . '</div>'
                . '</div>';
        } else {
            $this->content = '<div class="input-actions">'
                .   '<div>' . $this->content . '</div>'
                .   '<div>' . $this->action($attr) . '</div>'
                . '</div>';
        }

        return $this;
    }

    /**
     * Action
     * 
     * @param array $attr
     * 
     * @return string
     */
    public function action(array $attr): string
    {
        if (isset($attr['checkbox'])) {
            return $this->checkbox($attr);
        }

        $label = '';

        if (isset($attr['icon'])) {
            $label .= '<i class="' . $attr['icon'] . '" aria-hidden="true"></i>';
            unset($attr['icon']);
        }

        if (isset($attr['title'])) {
            $label .= '<span class="visually-hidden">' . $attr['title'] . '</span>';
        }

        if (isset($attr['name'])) {
            $attr['type'] ??= 'button';
        }

        $tagName = isset($attr['href'])
            ? 'a'
            : (isset($attr['type']) ? 'button' : 'div');

        return '<' . $tagName . $this->attr(['class' => 'btn-inline'], $attr) . '>' . $label . '</' . $tagName . '>';
    }

    /**
     * Checkbox
     * 
     * @param array $attr
     * 
     * @return string
     */
    public function checkbox(array $attr): string
    {
        unset($attr['checkbox']);

        $label    = '';
        $hidden   = false;
        $icon     = $this->extract($attr, 'icon');
        $icon_alt = $this->extract($attr, 'icon_alt');

        if ($icon) {
            $hidden = true;

            if ($icon_alt) {
                $label .= '<i class="' . $icon . ' d-on-not-checked" aria-hidden="true"></i>';
                $label .= '<i class="' . $icon_alt . ' d-on-checked" aria-hidden="true"></i>';
            } else {
                $label .= '<i class="' . $icon . '" aria-hidden="true"></i>';
            }
        }

        if (isset($attr['title'])) {
            $label .= '<span class="visually-hidden">' . $attr['title'] . '</span>';
        }

        return '<label class="btn-inline' . ($hidden ? ' checkbox-hidden' : '') . '">'
            . '<input type="checkbox"' . $this->attr(['class' => 'input-checkbox'], $attr)  . '>'
            . $label
            . '</label>';
    }

    /**
     * Render
     * 
     * @return string
     */
    public function render(): string
    {
        return $this->content;
    }

    /**
     * To string representation.
     * 
     * @return string
     */
    public function __toString(): string
    {
        return $this->content;
    }

    /**
     * Merge attributes
     */
    protected function attr(array $a, array $b): string
    {
        if ($b) {
            if (isset($b['class'])) {
                $a['class'] .= ' ' . $b['class'];
                unset($b['class']);
            }

            $a = array_merge($a, $b);
        }

        $output  = '';
        foreach ($a as $n => $v) {
            $output .=  ' ' . $n . '="' . $v . '"';
        }

        return $output;
    }

    /**
     * Extract attributes
     */
    protected function extract(array &$attr, string $name, mixed $value = ''): mixed
    {
        if (isset($attr[$name])) {
            $value = $attr[$name];
            unset($attr[$name]);
        }

        return $value;
    }
}
