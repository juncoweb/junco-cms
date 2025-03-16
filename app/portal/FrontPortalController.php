<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Controller;

class FrontPortalController extends Controller
{
	/**
	 * Index
	 */
	public function index()
	{
		return $this->view(null, (new PortalModel)->getIndexData());
	}
}
