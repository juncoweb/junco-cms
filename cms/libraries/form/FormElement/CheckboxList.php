<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Form\FormElement;

class CheckboxList extends FormElement
{
	/**
	 * Constructor
	 * 
	 * @param string $name
	 * @param array  $default
	 * @param array  $options
	 * @param array  $attr
	 */
	public function __construct(
		protected string $name,
		array $options,
		array $default = [],
		array $attr = []
	) {
		$html = $this->extract($attr, 'check-all') && $options
			? '<div class="input-checkall"><label><input type="checkbox" control-felem="check-all" data-checkall="' . $name . '" class="input-checkbox"/></label></div>'
			: '';
		$class = $this->extract($attr, 'inline') ? '' : ' input-block';
		$attr  = $this->attr([
			'type' => 'checkbox',
			'name' => $name . '[]',
		], $attr);

		foreach ($options as $value => $label) {
			$html .= '<label class="input-label' . $class . '"><input' . $attr . ' value="' . $value . '" class="input-checkbox"' . (in_array($value, $default) ? ' checked' : '') . '/> ' . $label . '</label>';
		}


		$this->html  = $html;
	}
}
