<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Form\FormElement\CustomElement;

return function (array $attr): CustomElement {
	return new CustomElement('', '<div class="btn-group" data-set="' . $attr['name'] . '">'
		. '<button control-form="toggle" title="' . ($t = _t('Show')) . '" aria-label="' . $t . '" class="btn">JSON</button>'
		. '<button control-form="json" title="' . ($t = _t('Create')) . '" aria-label="' . $t . '" class="btn"><i class="fa-solid fa-plus"></i></button>'
		. '<button control-form="json" data-json="edit" title="' . ($t = _t('Edit')) . '" aria-label="' . $t . '" class="btn"><i class="fa-solid fa-pencil"></i></button>'
		. '</div>'
		. '<textarea'
		. ' id="' . $attr['name'] . '"'
		. ' name="' . $attr['name'] . '"'
		. ' class="input-field"'
		. ' control-felem="auto-grow"'
		. ' data-options="' . $attr['options'] . '"'
		. ' style="display: none; margin-top: 2px;"'
		. '>' . $attr['value'] . '</textarea>');
};
