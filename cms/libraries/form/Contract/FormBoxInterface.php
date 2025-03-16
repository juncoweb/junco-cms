<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Form\Contract;

interface FormBoxInterface
{
	/**
	 * Tab
	 * 
	 * @param string  $tab
	 * @param ?string $tabpanel
	 * 
	 * @return void
	 */
	public function tab(string $tab = '', ?string $tabpanel = ''): void;

	/**
	 * Render
	 * 
	 * @param string $css
	 */
	public function render(): string;

	/**
	 * To string representation.
	 * 
	 * @return string
	 */
	public function __toString(): string;
}
