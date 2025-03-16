<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Form\Contract\FormActionsInterface;
use Junco\Form\Contract\FormBoxInterface;
use Junco\Form\Contract\FormElementsInterface;
use Junco\Form\Contract\FormInterface;

class Form
{
	/**
	 * Get
	 */
	public static function get(string $snippet = '', string|int|false $form_id = ''): FormInterface
	{
		return snippet('form', $snippet, $form_id);
	}

	/**
	 * Get
	 */
	public static function getElements(string $snippet = ''): FormElementsInterface
	{
		return snippet('form#elements', $snippet);
	}

	/**
	 * Get
	 */
	public static function getBox(string $snippet = '', string|int $box_id = ''): FormBoxInterface
	{
		return snippet('form#box', $snippet, $box_id);
	}

	/**
	 * Get
	 */
	public static function getActions(string $snippet = ''): FormActionsInterface
	{
		return snippet('form#actions', $snippet);
	}
}
