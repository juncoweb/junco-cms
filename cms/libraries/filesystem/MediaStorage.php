<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Filesystem;

class MediaStorage
{
	// vars
	protected string $media_path;
	protected string $site_url;
	protected string $site_baseurl;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$config				= config('site');
		$this->media_path	= SYSTEM_ABSPATH . SYSTEM_MEDIA_PATH;
		$this->site_url		= $config['site.url'] . SYSTEM_MEDIA_PATH;
		$this->site_baseurl = $config['site.baseurl'] . SYSTEM_MEDIA_PATH;
	}

	/**
	 * Media path
	 * 
	 * @param string $path
	 */
	function getPath(string $path = ''): string
	{
		if ($path) {
			$path = $this->searchAndReplaceConfig($path);
		}

		return $this->media_path . $path;
	}

	/**
	 * Get Url
	 * 
	 * @param string $path
	 * @param bool   $absolute
	 * 
	 * @return string
	 */
	function getUrl(string $path = '', bool $absolute = false): string
	{
		if ($path) {
			$path = $this->searchAndReplaceConfig($path);
		}

		return ($absolute ? $this->site_url : $this->site_baseurl) . $path;
	}

	/**
	 * Serach and remplace config
	 * 
	 * @param string $path
	 * 
	 * @return string
	 */
	protected function searchAndReplaceConfig(string $path): string
	{
		$part = explode('/', $path, 2);

		if (strpos($part[0], '.') !== false) {
			$part[0] = config($part[0]);

			if ($part[0] !== null) {
				return implode($part);
			}
		}

		return $path;
	}
}
