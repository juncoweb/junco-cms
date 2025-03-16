<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Tabs\TabsInterface;

abstract class TabsBase implements TabsInterface
{
	// vars
	protected array $tablist	= [];
	protected array $tabpanel	= [];
	protected array $options	= [];

	/**
	 * Constructor
	 * 
	 * @param string|array $id
	 * @param array        $options
	 */
	public function __construct(string|array $id = '', array $options = [])
	{
		if (is_array($id)) {
			$options = $id;
		} elseif ($id) {
			$options['id'] = $id;
		}

		$this->options = array_merge([
			'id' => 'tabs',
		], $options);
	}

	/**
	 * Tab
	 * 
	 * @param string $tab
	 * @param string $tabpanel
	 * 
	 * @return void
	 */
	public function tab(string $tab, string $tabpanel = ''): void
	{
		$this->tablist[]  = $tab;
		$this->tabpanel[] = $tabpanel;
	}
}
