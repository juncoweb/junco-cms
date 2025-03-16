<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Controller;

class FrontContactController extends Controller
{
	/**
	 * Index
	 */
	public function index()
	{
		return $this->view(null, (new FrontContactModel)->getIndexData());
	}

	/**
	 * Message
	 */
	public function message()
	{
		return $this->view(null, (new FrontContactModel)->getMessageData());
	}

	/**
	 * Take
	 */
	public function take()
	{
		return $this->middleware('form.security')
			?: $this->wrapper(fn() => (new ContactModel)->take());
	}
}
