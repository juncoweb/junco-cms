<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */


use Junco\Mvc\Controller;

class AdminAssetsThemesController extends Controller
{
	/**
	 * Index
	 */
	public function index()
	{
		return $this->view();
	}

	/**
	 * List
	 */
	public function list()
	{
		return $this->view(null, (new AdminAssetsThemesModel)->getListData());
	}

	/**
	 * Create
	 */
	public function create()
	{
		return $this->view(null, (new AdminAssetsThemesModel)->getCreateData());
	}

	/**
	 * Save
	 */
	public function save()
	{
		return $this->middleware('form.security')
			?: $this->wrapper(fn() => (new AssetsThemesModel)->save());
	}

	/**
	 * Copy
	 */
	public function copy()
	{
		return $this->view('Create', (new AdminAssetsThemesModel)->getCopyData());
	}

	/**
	 * Confirm delete
	 */
	public function confirmDelete()
	{
		return $this->view(null, (new AdminAssetsThemesModel)->getConfirmDeleteData());
	}

	/**
	 * Delete
	 */
	public function delete()
	{
		return $this->middleware('form.security')
			?: $this->wrapper(fn() => (new AssetsThemesModel)->delete());
	}

	/**
	 * Confirm compile
	 */
	public function confirmCompile()
	{
		return $this->view(null, (new AdminAssetsThemesModel)->getConfirmCompileData());
	}

	/**
	 * Compile
	 */
	public function compile()
	{
		return $this->middleware('form.security')
			?: $this->wrapper(fn() => (new AssetsThemesModel)->compile());
	}

	/**
	 * Confirm select
	 */
	public function confirmSelect()
	{
		return $this->view(null, (new AdminAssetsThemesModel)->getConfirmSelectData());
	}

	/**
	 * Select
	 */
	public function select()
	{
		return $this->middleware('form.security')
			?: $this->wrapper(fn() => (new AssetsThemesModel)->select());
	}
}
