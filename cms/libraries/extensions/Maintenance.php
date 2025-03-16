<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Extensions;

class Maintenance
{
	// vars
	protected bool   $status;
	protected string $htaccess;
	protected string $__htaccess;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$prefix = config('extensions-maintenance.prefix') ?: 'maintenance';
		$this->htaccess		= SYSTEM_ABSPATH . '.htaccess';
		$this->__htaccess	= sprintf(SYSTEM_ABSPATH . '%s.htaccess', $prefix);
		$this->status		= is_file($this->__htaccess);
	}

	/**
	 * Get
	 */
	public function getStatus(): bool
	{
		return $this->status;
	}

	/**
	 * Toggles the status of the maiintenance mode
	 */
	public function toggleStatus(?bool $status = null): bool
	{
		if ($status === null) {
			$status = !$this->status;
		}

		if ($status) {
			return $this->enableStatus();
		}
		return $this->disableStatus();
	}

	/**
	 * 
	 */
	protected function enableStatus(): bool
	{
		if ($this->status === true) {
			return true;
		}

		if (
			is_file($this->htaccess)
			&& rename($this->htaccess, $this->__htaccess)
			&& file_put_contents($this->htaccess, $this->getContent())
		) {
			$this->status = true;
			return true;
		}

		return false;
	}

	/**
	 * 
	 */
	protected function disableStatus(): bool
	{
		if ($this->status === false) {
			return true;
		}

		if (
			is_file($this->__htaccess)
			&& rename($this->__htaccess, $this->htaccess)
		) {
			$this->status = false;
			return true;
		}

		return false;
	}

	/**
	 * 
	 */
	protected function getContent(): string
	{
		$ip = curuser()->getIp();
		$file = config('site.baseurl') . config('extensions-maintenance.file');

		return '
<IfModule mod_rewrite.c>
RewriteEngine on
RewriteCond %{REMOTE_ADDR} !^' . $ip . '
RewriteCond %{REQUEST_URI} !' . $file . '$ [NC]
RewriteRule .* ' . $file . ' [L]
</IfModule>
' . file_get_contents($this->__htaccess);
	}
}
