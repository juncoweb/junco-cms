<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Controller;

class FrontUsysPasswordController extends Controller
{
	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->authenticate(-1);
	}

	/**
	 * Index
	 */
	public function reset()
	{
		return $this->view(null, (new FrontUsysPasswordModel)->getResetData());
	}

	/**
	 * Reset
	 */
	public function sendToken()
	{
		return $this->middleware('form.security')
			?: $this->wrapper(fn() => (new UsysPasswordModel)->sentToken());
	}

	/**
	 * Edit
	 */
	public function edit()
	{
		return $this->view(null, (new FrontUsysPasswordModel)->getEditData());
	}

	/**
	 * Update
	 */
	public function update()
	{
		return $this->middleware('form.security')
			?: $this->wrapper(fn() => (new UsysPasswordModel)->update());
	}
}
