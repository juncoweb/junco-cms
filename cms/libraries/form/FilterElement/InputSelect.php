<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Form\FilterElement;

class InputSelect extends FilterElement
{
	// vars
	protected string $html = '';

	/**
	 * Constructor
	 *
	 * @param string  $name
	 * @param ?string $label
	 * @param array	  $attr
	 */
	public function __construct(
		?string $input_name = null,
		mixed   $input_value = null,
		string  $select_name = '',
		array   $options = [],
		?string $default = null
	) {
		if (!isset($options[$default])) {
			$default = array_key_first($options);
		}

		$html = '<div class="btn-group" control-felem="select">'
			.  '<input type="text" name="' . $input_name . '" value="' . $input_value . '" aria-label="' . _t('Search') . '" class="btn">'
			.  '<button type="submit" class="btn" data-select-label>' . $options[$default] . '</button>'
			.  '<button type="button" class="btn dropdown-toggle"></button>'
			.  '<div class="dropdown-menu" style="display: none;">'
			.    '<input type="hidden" name="' . $select_name . '" value="' . $default . '">'
			.    $this->renderMenu($options, $default)
			.  '</div>'
			. '</div>';

		$this->html = '<div class="btn-group">' . $html . '</div>';
	}

	/**
	 * Render
	 * 
	 * @param array $options
	 * 
	 * @return string
	 */
	protected function renderMenu(array $options, string $default)
	{
		$html = '';
		foreach ($options as $value => $label) {
			$html .= '<li data-select-value="' . $value . '"' . ($value === $default ? ' class="selected"' : '') . '>'
				. '<a href="javascript:void(0)"><i></i>' . $label . '</a>'
				. '</li>';
		}

		return '<ul>' .  $html . '</ul>';
	}
}
