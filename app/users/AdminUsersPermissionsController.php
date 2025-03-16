<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Controller;

class AdminUsersPermissionsController extends Controller
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
		return $this->view(null, (new AdminUsersPermissionsModel)->getListData());
	}

	/**
	 * Status
	 */
	public function status()
	{
		return $this->middleware('form.security')
			?: $this->wrapper(fn() => (new UsersPermissionsModel)->status());
	}
}
