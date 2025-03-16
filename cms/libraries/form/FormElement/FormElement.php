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
	protected string  $name		= '';
	protected string  $html		= '';
	protected ?string $label	= null;
	protected bool    $required	= false;
	protected string  $help		= '';

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
	 * Render
	 * 
	 * @return string
	 */
	public function render(): string
	{
		return $this->html;
	}

	/**
	 * To string representation.
	 * 
	 * @return string
	 */
	public function __toString(): string
	{
		return $this->html;
	}

	/**
	 * Merge attributes
	 */
	protected function attr(array $a, array $b)
	{
		if ($b) {
			if (isset($b['class'])) {
				$a['class'] .= ' ' . $b['class'];
				unset($b['class']);
			}

			$a = array_merge($a, $b);
		}

		$html  = '';
		foreach ($a as $n => $v) {
			$html .=  ' ' . $n . '="' . $v . '"';
		}

		return $html;
	}

	/**
	 * Extract attributes
	 */
	protected function extract(array &$attr, string $name, $value = '')
	{
		if (isset($attr[$name])) {
			$value = $attr[$name];
			unset($attr[$name]);
		}

		return $value;
	}
}
