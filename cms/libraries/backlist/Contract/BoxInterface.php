<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Backlist\Contract;

interface BoxInterface
{
	/**
	 * Constructor
	 * 
	 * @param string $id
	 */
	public function __construct(string $id = '');

	/**
	 * Actions
	 * 
	 * @return ActionsInterface
	 */
	public function getActions(): ActionsInterface;

	/**
	 * Render
	 * 
	 * @param string $content
	 * 
	 * @return string
	 */
	public function render(string $content = ''): string;
}
