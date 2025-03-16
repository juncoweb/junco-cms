<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Form\FormElement;

class Radio extends FormElement
{
	/**
	 * Constructor
	 *
	 * @param string $name
	 * @param string $default
	 * @param array	 $options
	 * @param array	 $attr
	 */
	public function __construct(
		protected string $name,
		string $default,
		array  $options,
		array  $attr = []
	) {
		$class = $this->extract($attr, 'inline', true) ? '' : ' input-block';
		$attr  = $this->attr([
			'type'	=> 'radio',
			'name'	=> $name,
			'class'	=> 'input-radio'
		], $attr);
		$html  = '';

		foreach ($options as $value => $caption) {
			$html .= '<label class="input-label' . $class . '"><input' . $attr . ' value="' . $value . '"' . ($value == $default ? ' checked' : '') . '/> ' . $caption . '</label>';
		}

		$this->html = $html;
	}
}
