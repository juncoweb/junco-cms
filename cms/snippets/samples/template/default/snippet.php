<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

class template_samples_default_snippet extends Template
{
	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		$this->assets->options(['hash' => '']);
		$this->content = '';
		$this->view = __DIR__ . '/view.html.php';
	}
}
