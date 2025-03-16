<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Controller;

class AdminExtensionsChangesController extends Controller
{
	/**
	 * Index
	 */
	public function index()
	{
		return $this->view(null, (new AdminExtensionsChangesModel)->getIndexData());
	}

	/**
	 * List
	 */
	public function list(?array $data = null)
	{
		return $this->view(null, (new AdminExtensionsChangesModel)->setData($data)->getListData());
	}

	/**
	 * Create
	 */
	public function create()
	{
		return $this->view('SaveForm', (new AdminExtensionsChangesModel)->getCreateData());
	}

	/**
	 * Edit
	 */
	public function edit()
	{
		return $this->view('SaveForm', (new AdminExtensionsChangesModel)->getEditData());
	}

	/**
	 * Save
	 */
	public function save()
	{
		return $this->middleware('form.security')
			?: $this->wrapper(fn() => (new ExtensionsChangesModel)->save());
	}

	/**
	 * Confirm delete
	 */
	public function confirmDelete()
	{
		return $this->view(null, (new AdminExtensionsChangesModel)->getConfirmDeleteData());
	}

	/**
	 * Delete
	 */
	public function delete()
	{
		return $this->middleware('form.security')
			?: $this->wrapper(fn() => (new ExtensionsChangesModel)->delete());
	}
}
