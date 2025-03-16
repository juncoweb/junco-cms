<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Http\Emitter\SapiEmitter;

class Debugger
{
	/**
	 * Constructor
	 */
	public function __construct()
	{
		// error handler
		if (SYSTEM_HANDLE_ERRORS) {
			error_reporting(0);
			ini_set('display_errors', 0);
			set_error_handler(function (
				int    $code,
				string $message,
				string $file = '',
				int    $line = 0,
				array  $context = []
			) {
				$level   = $this->codeToLevel($code);
				$message = $this->codeToString($code) . ': ' . $message;
				$context = [
					'code'		=> $code,
					'file'		=> $file,
					'line'		=> $line,
					'backtrace' => $this->getTraceAsString(debug_backtrace())
				];

				app('logger')->log($level, $message, $context);
			});
		} else {
			error_reporting(E_ALL);
			ini_set('display_errors', 1);
		}

		// exception handler
		set_exception_handler(function (Throwable $e) {
			if ($e instanceof Error) {
				$this->alert($this->handleThrowableError($e), $e->getCode());
			} else {
				$this->alert($e->getMessage(), $e->getCode());
			}
		});
	}

	/**
	 * Returns a message from a numeric code
	 * 
	 * @param int $code
	 * 
	 * @return string
	 */
	public function getMessageFromCode(int $code = 0): string
	{
		switch ($code) {
			case 401:
				return sprintf(
					_t('Please, you must %s or %s'),
					'<a href="' . url('/usys/login', ['redirect' => -1]) . '">' . _t('Log in') . '</a>',
					'<a href="' . url('/usys/signup') . '">' . _t('Sign Up') . '</a>'
				);
			case 403:
				return _t('Access denied.');
			case 404:
				return _t('The requested was not found on this server.');
			case 500:
				return sprintf(
					_t('Fatal error in safety. Please help us to fix it by contacting the %sadministration%s.'),
					'<a href="' . url('/contact') . '" target="_blank">',
					'</a>'
				);
			default:
				return _t('A fatal error or a security failure has occurred.');
		}
	}

	/**
	 * Alert
	 * 
	 * @param string $message
	 * @param int $code
	 */
	public function alert(string $message = '', int $code = 0)
	{
		try {
			// I show the basic template.
			if (router()->isFormat('template')) {
				$response = snippet('template')->alert($message, $code);
				(new SapiEmitter)->emit($response);
				die;
			}
		} catch (Throwable $e) {
		}

		die(sprintf('%d - %s', $code, $message));
	}

	/**
	 * Get response from throwable
	 * 
	 * @param Throwable $e
	 */
	public function getResponseFromThrowable(Throwable $e)
	{
		try {
			$code = $e->getCode();

			if ($e instanceof Error) {
				$message = $this->handleThrowableError($e);
				return System::getOutput()->alert($message, $code);
			}

			$message = $e->getMessage();
			return System::getOutput()->message($message, $code);
		} catch (Throwable $e) {
		}

		$this->alert($message, $code);
	}

	/**
	 * Get
	 * 
	 * @param array $traces
	 * 
	 * @return string
	 */
	protected function getTraceAsString(array $traces): string
	{
		foreach ($traces as $i => $trace) {
			if (isset($trace['args'])) {
				if (is_array($trace['args'])) {
					$trace['args'] = $this->getArgsAsString($trace['args']);
				}
			} else {
				$trace['args'] = '';
			}
			if (isset($trace['class'])) {
				$trace['function'] = $trace['class'] . $trace['type'] . $trace['function'];
			}

			$traces[$i] = sprintf(
				'#%d %s(%d): %s(%s)',
				$i,
				$trace['file'] ?? '',
				$trace['line'] ?? 0,
				$trace['function'],
				$trace['args']
			);
		}

		return implode("\n", $traces);
	}

	/**
	 * Get
	 * 
	 * @param array $args
	 * 
	 * @return string
	 */
	protected function getArgsAsString(array $args): string
	{
		foreach ($args as $i => $value) {
			$type = gettype($value);
			switch ($type) {
				case 'integer':
				case 'double':
					break;
				case 'object':
					$args[$i] = get_class($value);
					break;
				case 'string':
					if (strlen($value) > 17) {
						$args[$i] = substr($value, 0, 17) . '...';
					}
					$args[$i] = "'$value'";
					break;
				case 'boolean':
					$args[$i] = $value ? 'true' : 'false';
					break;
				default:
					$args[$i] = ucfirst($type);
					break;
			}
		}

		return implode(', ', $args);
	}

	/**
	 * Handles captured errors
	 * 
	 * @param Error $e
	 * 
	 * @return string
	 */
	protected function handleThrowableError(Error $e): string
	{
		if (SYSTEM_HANDLE_ERRORS) {
			try {
				app('logger')->alert(sprintf('%s: %s', get_class($e), $e->getMessage()), [
					'code'		=> $e->getCode(),
					'file'		=> $e->getFile(),
					'line'		=> $e->getLine(),
					'backtrace' => $e->getTraceAsString()
				]);

				return $this->getMessageFromCode($e->getCode());
			} catch (Throwable $e) {
				return 'A fatal error or a security failure has occurred.';
			}
		} else {
			return str_replace("\n", '<br />', $e->__toString());
		}
	}

	/**
	 * Code To String
	 * 
	 * @param int $code
	 * 
	 * @return string
	 */
	protected function codeToString(int $code): string
	{
		switch ($code) {
			case E_ERROR:
				return 'E_ERROR';
			case E_WARNING:
				return 'E_WARNING';
			case E_PARSE:
				return 'E_PARSE';
			case E_NOTICE:
				return 'E_NOTICE';
			case E_CORE_ERROR:
				return 'E_CORE_ERROR';
			case E_CORE_WARNING:
				return 'E_CORE_WARNING';
			case E_COMPILE_ERROR:
				return 'E_COMPILE_ERROR';
			case E_COMPILE_WARNING:
				return 'E_COMPILE_WARNING';
			case E_USER_ERROR:
				return 'E_USER_ERROR';
			case E_USER_WARNING:
				return 'E_USER_WARNING';
			case E_USER_NOTICE:
				return 'E_USER_NOTICE';
			case E_STRICT:
				return 'E_STRICT';
			case E_RECOVERABLE_ERROR:
				return 'E_RECOVERABLE_ERROR';
			case E_DEPRECATED:
				return 'E_DEPRECATED';
			case E_USER_DEPRECATED:
				return 'E_USER_DEPRECATED';
		}
	}

	/**
	 * Code To Level
	 * 
	 * @param int $code
	 * 
	 * @return string
	 */
	protected function codeToLevel(int $code): string
	{
		switch ($code) {
			default:
			case E_ERROR:
			case E_CORE_ERROR:
			case E_COMPILE_ERROR:
				return Logger::CRITICAL;

			case E_PARSE:
				return Logger::ALERT;

			case E_USER_ERROR:
			case E_RECOVERABLE_ERROR:
				return Logger::ERROR;

			case E_WARNING:
			case E_CORE_WARNING:
			case E_COMPILE_WARNING:
			case E_USER_WARNING:
				return Logger::WARNING;

			case E_NOTICE:
			case E_USER_NOTICE:
			case E_STRICT:
			case E_DEPRECATED:
			case E_USER_DEPRECATED:
				return Logger::NOTICE;
		}
	}
}
