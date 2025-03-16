<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Psr\Log\LoggerInterface;

class Logger implements LoggerInterface
{
	// const
	const EMERGENCY = 'emergency';
	const ALERT     = 'alert';
	const CRITICAL  = 'critical';
	const ERROR     = 'error';
	const WARNING   = 'warning';
	const NOTICE    = 'notice';
	const INFO      = 'info';
	const DEBUG     = 'debug';
	//
	const LEVELS    = [
		self::EMERGENCY => 'emergency',
		self::ALERT     => 'alert',
		self::CRITICAL  => 'critical',
		self::ERROR     => 'error',
		self::WARNING   => 'warning',
		self::NOTICE    => 'notice',
		self::INFO      => 'info',
		self::DEBUG     => 'debug',
	];

	/**
	 * Logs with an arbitrary level.
	 *
	 * @param mixed  $level
	 * @param string|\Stringable $message
	 * @param array  $context
	 * 
	 * @return void
	 */
	public function log($level, string|\Stringable $message, array $context = []): void
	{
		if (!isset(self::LEVELS[$level])) {
			throw new InvalidArgumentException('Logging level is invalid');
		}

		$server = request()?->getServerParams();
		if (isset($server['REMOTE_ADDR'])) {
			$context['ip'] = $server['REMOTE_ADDR'];
		}
		if (isset($server['HTTP_HOST'])) {
			$context['hostname'] = $server['HTTP_HOST'];
		}
		if (isset($server['SERVER_PORT'])) {
			$context['port'] = $server['SERVER_PORT'];
		}
		if (isset($server['HTTP_USER_AGENT'])) {
			$context['user_agent'] = $server['HTTP_USER_AGENT'];
		}
		if (isset($server['HTTP_REFERER'])) {
			$context['referrer'] = $server['HTTP_REFERER'];
		}
		if (isset($server['REQUEST_URI'])) {
			$context['uri'] = $server['REQUEST_URI'];
		}

		$this->store($level, $message, json_encode($context, JSON_UNESCAPED_SLASHES));
	}

	/**
	 * System is unusable.
	 *
	 * @param string|\Stringable $message
	 * @param array  $context
	 * 
	 * @return void
	 */
	public function emergency(string|\Stringable $message, array $context = []): void
	{
		$this->log(self::EMERGENCY, $message, $context);
	}

	/**
	 * Action must be taken immediately.
	 *
	 * Example: Entire website down, database unavailable, etc. This should
	 * trigger the SMS alerts and wake you up.
	 *
	 * @param string|\Stringable $message
	 * @param array  $context
	 * 
	 * @return void
	 */
	public function alert(string|\Stringable $message, array $context = []): void
	{
		$this->log(self::ALERT, $message, $context);
	}

	/**
	 * Critical conditions.
	 *
	 * Example: Application component unavailable, unexpected exception.
	 *
	 * @param string|\Stringable $message
	 * @param array  $context
	 * 
	 * @return void
	 */
	public function critical(string|\Stringable $message, array $context = []): void
	{
		$this->log(self::CRITICAL, $message, $context);
	}

	/**
	 * Runtime errors that do not require immediate action but should typically
	 * be logged and monitored.
	 *
	 * @param string|\Stringable $message
	 * @param array  $context
	 * 
	 * @return void
	 */
	public function error(string|\Stringable $message, array $context = []): void
	{
		$this->log(self::ERROR, $message, $context);
	}

	/**
	 * Exceptional occurrences that are not errors.
	 *
	 * Example: Use of deprecated APIs, poor use of an API, undesirable things
	 * that are not necessarily wrong.
	 *
	 * @param string|\Stringable $message
	 * @param array  $context
	 * 
	 * @return void
	 */
	public function warning(string|\Stringable $message, array $context = []): void
	{
		$this->log(self::WARNING, $message, $context);
	}

	/**
	 * Normal but significant events.
	 *
	 * @param string|\Stringable $message
	 * @param array  $context
	 * 
	 * @return void
	 */
	public function notice(string|\Stringable $message, array $context = []): void
	{
		$this->log(self::NOTICE, $message, $context);
	}

	/**
	 * Interesting events.
	 *
	 * Example: User logs in, SQL logs.
	 *
	 * @param string|\Stringable $message
	 * @param array  $context
	 * 
	 * @return void
	 */
	public function info(string|\Stringable $message, array $context = []): void
	{
		$this->log(self::INFO, $message, $context);
	}

	/**
	 * Detailed debug information.
	 *
	 * @param string|\Stringable $message
	 * @param array  $context
	 * @return void
	 */
	public function debug(string|\Stringable $message, array $context = []): void
	{
		if (SYSTEM_HANDLE_ERRORS) {
			$this->log(self::DEBUG, $message, $context);
		}
	}

	/**
	 * Store the data.
	 *
	 * @param string $level
	 * @param string|\Stringable $message
	 * @param string $context
	 * 
	 * @return void
	 */
	protected function store(string $level, string|\Stringable $message, string $context): void
	{
		$dir	 = app('system')->getLogPath();
		$file	 = $dir . (config('logger.log_file') ?: 'error_log');

		if (is_file($file)) {
			$lines = file($file);
		} else {
			$lines = [];
		}

		$count	 = count($lines);
		$id      = ($count ? (int)$lines[$count - 1] : 0) + 1;
		$added   = time();
		$message = filter_var($message, FILTER_SANITIZE_SPECIAL_CHARS);
		$data    = [
			'id'      => $id,
			'status'  => '0',
			'added'   => $added,
			'level'	  => $level,
			'message' => $message,
			'context' => $context,
		];

		is_dir($dir) or mkdir($dir, SYSTEM_MKDIR_MODE, true);
		error_log(implode('|', $data) . PHP_EOL, 3, $file);
	}
}
