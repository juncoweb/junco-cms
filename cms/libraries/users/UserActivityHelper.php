<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Users;

class UserActivityHelper
{
	/**
	 * Gets a text from the message type and code.
	 * 
	 * @param string $type
	 * @param int    $code
	 * 
	 * @return string
	 */
	public static function getCodeMessages(string $type, int $code)
	{
		if ($type == 'login') {
			switch ($code) {
				case  0:
					return _t('The user has successfully logged in.');
				case -1:
					return _t('The user has not been found.');
				case -2:
					return _t('The user is not active.');
				case -3:
					return _t('The automatically generated user has not been found.');
				case -7:
					return _t('The user has entered an invalid password.');
			}
		} elseif ($type == 'token') {
			switch ($code) {
				case  -1:
					return _t('The token has no format.');
				case -10:
					return _t('The token has not been found.');
				case -11:
					return _t('The token does not match the type of activity.');
				case -12:
					return _t('The token has already been used.');
				case -13:
					return _t('The token has expired.');
				case -14:
					return _t('The token has failed validation.');
			}
		} elseif (!$code) {
			return _t('The action has been completed correctly.');
		}

		return 'Unknown.';
	}
}
