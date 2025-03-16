<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Form\FormElement;

class Suite extends FormElement
{
	/**
	 * Constructor
	 *
	 * @param string       $name
	 * @param array|string $default
	 * @param array	       $options
	 * @param array	       $attr
	 */
	public function __construct(
		protected string $name,
		array|string $default = [],
		array $options = [],
		array $attr = []
	) {
		$html = '';

		if (is_array($default)) {
			$selected = implode(',', $default);
		} else {
			$selected = $default;
			$default  = explode(',', $default);
		}

		foreach ($options as $value => $caption) {
			$html .= '<label data-value="' . $value . '" class="input-tag' . (in_array($value, $default) ? ' selected' : '') . '">' . $caption . '</label>';
		}

		$this->html = '<div control-felem="suite" data-name="' . $name . '" data-selected="' . $selected . '" class="fe-suite">'
			. '<ul><li></li><li>' . $html . '</li><li>'
			. '<div title="' . _t('Swap') . '"><i class="fa-solid fa-chevron-right"></i></div>'
			. '<div title="' . _t('Reset') . '"><i class="fa-solid fa-rotate-left"></i></div>'
			. '<div title="' . _t('Check all') . '"><input type="checkbox" class="input-checkbox"></div>'
			. '<div title="' . _t('Back') . '"><i class="fa-solid fa-chevron-left"></i></div>'
			. '</li></ul>'
			. '</div>';
	}

	/**
	 * Get
	 */
	public function getLabel(): string
	{
		if ($this->label) {
			return '<label class="input-label">' . $this->label . '</label>';
		}

		return $this->label;
	}
}
