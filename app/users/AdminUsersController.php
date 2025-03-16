<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Controller;

class AdminUsersController extends Controller
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
		return $this->view(null, (new AdminUsersModel)->getListData());
	}

	/**
	 * Create
	 */
	public function create()
	{
		return $this->view('SaveForm', (new AdminUsersModel)->getCreateData());
	}

	/**
	 * Edit
	 */
	public function edit()
	{
		return $this->view('SaveForm', (new AdminUsersModel)->getEditData());
	}

	/**
	 * Save
	 */
	public function save()
	{
		return $this->middleware('form.security')
			?: $this->wrapper(fn() => (new UsersModel)->save());
	}

	/**
	 * Status
	 */
	public function status()
	{
		return $this->middleware('form.security')
			?: $this->wrapper(fn() => (new UsersModel)->status());
	}

	/**
	 * Confirm delete
	 */
	public function confirmDelete()
	{
		return $this->view(null, (new AdminUsersModel)->getConfirmDeleteData());
	}

	/**
	 * Delete
	 */
	public function delete()
	{
		return $this->middleware('form.security')
			?: $this->wrapper(fn() => (new UsersModel)->delete());
	}

	/**
	 * Get
	 */
	public function users()
	{
		return (new AdminUsersModel)->getUsersData();
	}

	/**
	 * Get
	 */
	public function roles()
	{
		return (new AdminUsersModel)->getRolesData();
	}
}
