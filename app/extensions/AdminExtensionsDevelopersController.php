<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Controller;

class AdminExtensionsDevelopersController extends Controller
{
	/**
	 * Confirm delete
	 */
	public function confirmDelete()
	{
		return $this->view(null, (new AdminExtensionsDevelopersModel)->getDeleteData());
	}

	/**
	 * Delete
	 */
	public function delete()
	{
		return $this->middleware('form.security')
			?: $this->wrapper(fn() => (new ExtensionsDevelopersModel)->delete());
	}

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
		return $this->view(null, (new AdminExtensionsDevelopersModel)->getListData());
	}

	/**
	 * Create
	 */
	public function create()
	{
		return $this->view('SaveForm', [
			'title' => _t('Create'),
			'values' => null,
			'is_protected' => false,
		]);
	}

	/**
	 * Edit
	 */
	public function edit()
	{
		return $this->view('SaveForm', (new AdminExtensionsDevelopersModel)->getEditData());
	}

	/**
	 * Save
	 */
	public function save()
	{
		return $this->middleware('form.security')
			?: $this->wrapper(fn() => (new ExtensionsDevelopersModel)->save());
	}
}
