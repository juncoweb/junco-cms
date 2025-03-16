<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Responder\Contract;

interface AjaxTextInterface extends ResponderInterface
{
	/**
	 * Sets the text content.
	 * 
	 * @param string $content
	 * 
	 * @return void
	 */
	public function setContent(string $content): void;
}
