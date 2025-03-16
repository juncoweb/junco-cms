<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

interface MenusInterface
{
	/**
	 * Constructor
	 * 
	 * @param string $key
	 */
	public function __construct(string $key = '');

	/**
	 * Set to show only rows with edges.
	 * 
	 * @param bool $value
	 * 
	 * @return void
	 */
	public function setWithEdges(bool $value = true): void;

	/**
	 * Render
	 * 
	 * @return string
	 */
	public function render(): string;
}
