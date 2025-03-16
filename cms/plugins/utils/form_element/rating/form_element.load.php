<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Form\FormElement\CustomElement;

return function (array $attr = []) {
	$attr['value'] ??= 5;

	$html = '<div control-felem="rating" data-rating="' . $attr['value'] . '" data-name="' . $attr['name'] . '" class="rating rating-large rating-warning rating-pointer"></div>';
	$html .= '<input type="hidden" name="' . $attr['name'] . '" value="' . $attr['value'] . '"/>';

	return new CustomElement($attr['name'], $html);
};
