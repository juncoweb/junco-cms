<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

class tabs_master_default_snippet extends TabsBase
{
	/**
	 * Render
	 * 
	 * @return string
	 */
	public function render(): string
	{
		//
		$tablist  = '';
		$tabpanel = '';

		foreach ($this->tablist as $i => $label) {
			$panel_id = "{$this->options['id']}-panel-{$i}";
			$tablist  .= '<li role="tab" aria-controls="' . $panel_id . '">' . $label . '</li>';
			$tabpanel .= '<div role="tabpanel" id="' . $panel_id . '">' . $this->tabpanel[$i] . '</div>';
		}

		// free memory
		$this->tablist = [];
		$this->tabpanel = [];

		$class = 'tablist';
		if (!empty($this->options['class'])) {
			$class .= ' ' . $this->options['class'];
		}

		//
		return '<ul id="' . $this->options['id'] . '" class="' . $class . '">' . $tablist . '</ul>'
			. '<div id="' . $this->options['id'] . '-panel" class="tabpanel-group">' . $tabpanel . '</div>';
	}
}
