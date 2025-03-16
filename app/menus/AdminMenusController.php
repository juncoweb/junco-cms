<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Controller;

class AdminMenusController extends Controller
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
		return $this->view(null, (new AdminMenusModel)->getListData());
	}

	/**
	 * Create
	 */
	public function create()
	{
		return $this->view('SaveForm', (new AdminMenusModel)->getCreateData());
	}

	/**
	 * Edit
	 */
	public function edit()
	{
		return $this->view('SaveForm', (new AdminMenusModel)->getEditData());
	}

	/**
	 * Copy
	 */
	public function copy()
	{
		return $this->view('SaveForm', (new AdminMenusModel)->getCopyData());
	}

	/**
	 * Save
	 */
	public function save()
	{
		return $this->middleware('form.security')
			?: $this->wrapper(fn() => (new MenusModel)->save());
	}

	/**
	 * Status
	 */
	public function status()
	{
		return $this->middleware('form.security')
			?: $this->wrapper(fn() => (new MenusModel)->status());
	}

	/**
	 * Confirm delete
	 */
	public function confirmDelete()
	{
		return $this->view(null, (new AdminMenusModel)->getConfirmDeleteData());
	}

	/**
	 * Delete
	 */
	public function delete()
	{
		return $this->middleware('form.security')
			?: $this->wrapper(fn() => (new MenusModel)->delete());
	}

	/**
	 * Lock
	 */
	public function lock()
	{
		return $this->middleware('form.security')
			?: $this->wrapper(fn() => (new MenusModel)->lock());
	}

	/**
	 * Confirm
	 */
	public function confirmMaker()
	{
		return $this->view(null, (new MenusMakerModel)->getConfirmData());
	}

	/**
	 * Maker
	 */
	public function maker()
	{
		return $this->middleware('form.security')
			?: $this->wrapper(fn() => (new MenusMakerModel)->store());
	}
}
