<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Controller;

class AdminAssetsColorsController extends Controller
{
	/**
     * Index
     */
	public function index()
	{
		return $this->view(null, (new AdminAssetsColorsModel)->getIndexData());
	}

	/**
     * Palette
     */
	public function palette()
	{
		return $this->view(null, (new AdminAssetsColorsModel)->getPaletteData());
	}

	/**
     * Contrast
     */
	public function contrast()
	{
		return $this->view(null, (new AdminAssetsColorsModel)->getContrastData());
	}

	/**
     * Checker
     */
	public function checker()
	{
		return $this->view(null, (new AdminAssetsColorsModel)->getCheckerData());
	}
}
