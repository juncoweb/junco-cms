<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

class rating_utils_default_snippet
{
	/**
	 * Render
	 * 
	 * @param string|array $value
	 * 
	 * @return string
	 */
	public function render(string|array $value, array $attr = []): string
	{
		if (is_array($value)) {
			$value = implode('|', $value);
		}

		$class = 'rating';
		if ($attr) {
			if (
				isset($attr['color'])
				&& in_array($attr['color'], ['primary', 'secondary', 'info', 'success', 'warning', 'danger'])
			) {
				$class .= ' rating-' . $attr['color'];
			}
			if (
				isset($attr['size'])
				&& in_array($attr['size'], ['small', 'large'])
			) {
				$class .= ' rating-' . $attr['size'];
			}
		}

		return '<div class="' . $class . '" data-rating="' . $value . '" aria-label="' . _t('Rating $val of 5') . '"></div>';
	}
}
