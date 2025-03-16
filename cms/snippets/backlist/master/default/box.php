<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Backlist\Contract\ActionsInterface;
use Junco\Backlist\Contract\BoxInterface;

class backlist_master_default_box implements BoxInterface
{
	// vars
	protected $options = null;

	/**
	 * Constructor
	 * 
	 * @param string $id
	 */
	public function __construct(protected string $id = '')
	{
		$this->id = $id;
	}

	/**
	 * Actions
	 * 
	 * @return ActionsInterface
	 */
	public function getActions(): ActionsInterface
	{
		return $this->options = snippet('backlist#actions');
	}

	/**
	 * Render
	 * 
	 * @param string $content
	 * 
	 * @return string
	 */
	public function render(string $content = ''): string
	{
		return '<div id="' . ($this->id ? $this->id . '-' : '') . 'backlist-box" class="backlist-box panel">'
			.  (isset($this->options) ? $this->options->render() : '')
			.  '<div class="backlist-slot" backlist-slot aria-live="polite">' . $content . '</div>'
			. '</div>';
	}
}
