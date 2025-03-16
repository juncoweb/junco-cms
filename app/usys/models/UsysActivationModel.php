<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;
use Junco\Users\UserActivityToken;
use Junco\Users\UserHelper;

class UsysActivationModel extends Model
{
	// vars
	protected $db = null;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->db = db();
	}

	/**
	 * Send
	 */
	public function sendToken()
	{
		// data
		$this->filter(POST, [
			'option'			=> '',
			'email_username'	=> '',
			'new_email'			=> ''
		]);

		/**
		 * Instance 1
		 */
		$user = UserHelper::getUserFromInput($this->data['email_username']);

		if (!$user) {
			throw new Exception(_t('Invalid email/username.'));
		}

		if ($user['status'] === 'active') {
			throw new Exception(_t('Your account is active. Please, enter from the login.'));
		}

		if ($this->data['option'] == 1) {
			throw new Exception($this->obfuscateEmail($user['email']), 5);
		}

		/**
		 * Instance 2
		 */
		// validate
		if (
			$user['status'] === 'inactive'
			&& $this->data['new_email']
			&& $this->data['new_email'] !== $user['email']
		) {
			// vars
			$user['email'] = $this->data['new_email'];

			UserHelper::validateEmail($user['email']);
			UserHelper::isUniqueEmail($user['email'], $user['id']);

			// query
			$this->db->safeExec("UPDATE `#__users` SET email = ? WHERE id = ?", $user['email'], $user['id']);
		}

		// token
		$type = ($user['status'] === 'inactive') ? 'activation' : 'signup';

		$result = UserActivityToken::generateAndSend(
			$type,
			$user['id'],
			$user['email'],
			$user['fullname']
		);

		if (!$result) {
			throw new Exception(_t('An error has occurred in the mail server. Please, try again later.'));
		}

		return Xjs::redirectTo(url('/usys/message', ['op' => 'reset-act']));
	}

	/**
	 * Ofuscate
	 * 
	 * @param string $email
	 * @param int    $n			Number of visible characters.
	 * 
	 * @return string
	 */
	protected function obfuscateEmail(string $email, int $n = 1): string
	{
		$partial = explode('@', $email, 2);
		return substr($partial[0], 0, $n) . str_repeat('*', strlen($partial[0]) - $n) . '@' . $partial[1];
	}
}
