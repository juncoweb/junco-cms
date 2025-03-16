<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Form\Contract\FormBoxInterface;

class form_master_default_box implements FormBoxInterface
{
	// vars
	protected string $id		= '';
	protected array  $tabs		= [];
	protected array  $panels	= [];

	/**
	 * Constructor
	 * 
	 * @param string|int $id
	 */
	public function __construct(string|int $id = '')
	{
		if (!$id) {
			$id = 'form-box';
		} elseif (is_numeric($id)) {
			$id = 'form-box' . $id;
		} else {
			$id .= '-box';
		}

		$this->id = $id;
	}

	/**
	 * Tab
	 * 
	 * @param string  $tab
	 * @param ?string $tabpanel
	 * 
	 * @return void
	 */
	public function tab(string $tab = '', ?string $tabpanel = ''): void
	{
		if ($tabpanel === null) {
			$tabpanel = '<div class="dialog dialog-warning">' . _t('Please, fill in the first form before accessing at this.') . '</div>';
		}

		$this->tabs[] = $tab;
		$this->panels[] = $tabpanel;
	}

	/**
	 * Render
	 * 
	 * @param string $css
	 */
	public function render(): string
	{
		$count = count($this->panels);

		if ($count > 1) {
			$tabs = Tabs::get('', '', ['class' => 'responsive']);

			foreach ($this->tabs as $i => $tab) {
				$tabs->tab($tab, $this->panels[$i]);
			}

			$html = $tabs->render();
		} else {
			$html = ($count ? implode($this->panels) : '');
		}

		return '<div id="' . $this->id . '" class="form-box">' . $html . '</div>';
	}

	/**
	 * To string representation.
	 * 
	 * @return string
	 */
	public function __toString(): string
	{
		return $this->render();
	}
}
