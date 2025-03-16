<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Form\FormElement;

class File extends FormElement
{
	/**
	 * Constructor
	 *
	 * @param string $name
	 * @param array	 $options
	 */
	public function __construct(
		protected string $name,
		array $options = []
	) {
		$multiple = $this->extract($options, 'multiple');

		if ($multiple) {
			$multiple = ' multiple';
			$name    .= '[]';
		}

		if (empty($options['caption'])) {
			$options['caption'] = $multiple ? _t('Upload') : _t('Drop files here to upload');
		}

		$options = htmlentities(json_encode($options));

		$this->html = '<input type="file" name="' . $name . '" control-felem="file" data-options="' . $options . '" class="input-field"' . $multiple . '/>';
	}
}
