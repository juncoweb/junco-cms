<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Controller;

class AdminLanguageTranslationsController extends Controller
{
	/**
	 * Index
	 */
	public function index()
	{
		return $this->view(null, (new AdminLanguageTranslationsModel)->getIndexData());
	}

	/**
	 * List
	 */
	public function list(?array $data = null)
	{
		return $this->view(null, (new AdminLanguageTranslationsModel)->setData($data)->getListData());
	}

	/**
	 * Confirm download
	 */
	public function confirmDownload()
	{
		return $this->view(null, (new AdminLanguageTranslationsModel)->getDownloadData());
	}

	/**
	 * Download
	 */
	public function download()
	{
		return $this->middleware('form.security')
			?: $this->wrapper(fn() => (new AdminLanguageTranslationsModel)->download());
	}
}
