<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;
use Junco\Users\UserActivityToken;
use Junco\Usys\LoginWidgetCollector;

class FrontUsysModel extends Model
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
	 * Get
	 */
	public function getSignupData()
	{
		// vars
		$data = [
			'options'		=> config('usys.options'),
			'login_plugins' => config('usys.login_plugins'),
			'legal_url'		=> config('users.legal'),
			'user'			=> null,
			'widgets'		=> []
		];

		// security
		if (!config('users.default_ucid')) {
			return $data + ['error' => true];
		}

		$token = UserActivityToken::get(GET, 'signup', true);

		if ($token) {
			$user = $this->getUserFromToken($token);

			if ($user) {
				$data['user']			= $user;
				$data['token']			= $token;
				$data['login_plugins']	= '';
			}
		}

		if ($data['legal_url']) {
			$data['legal_url'] = UsysHelper::getUrl($data['legal_url']);
		}

		if ($data['login_plugins']) {
			$data['widgets'] = (new LoginWidgetCollector)->getAll($data['login_plugins']);
		}

		return $data;
	}

	/**
	 * Get
	 */
	public function getLoginData()
	{
		// data
		$this->filter(GET, ['redirect' => 'text']);

		$plugins = config('usys.login_plugins');

		return $this->data + [
			'options'		=> config('usys.login_options'),
			'not_expire'	=> config('users.not_expire'),
			'user'			=> $this->getUserFromSession(),
			'widgets'		=> ($plugins ? (new LoginWidgetCollector)->getAll($plugins) : [])
		];
	}

	/**
	 * Get
	 */
	public function getAutologinData()
	{
		try {
			$token = UserActivityToken::get(GET, 'autologin')->destroy();
			curuser()->login($token->user_id);
		} catch (Exception $e) {
			return ['options' => config('usys.options')];
		}

		redirect();
	}

	/**
	 * Get
	 */
	public function getMessageData()
	{
		// data
		$this->filter(GET, ['op' => '']);

		switch ($this->data['op']) {
			case 'signup':
				$title		= _t('Registration complete!');
				$message	= _t('Your account has been successfully created and an activation message has been sent to your email.');
				$attention	= _t('Check your email and follow the instructions. If you do not receive the message, check your SPAM settings on your account. Make sure your email account does not automatically delete SPAM.');
				break;

			case 'login':
				$title		= _t('Account activation pending');
				$message	= _t('Your user account is not active yet.') . '<br />' . sprintf(_t('Remember that, if necessary, you can request a new %sactivation message%s.'), '<a href="' . url('/usys.activation/reset') . '">', '</a>');
				$attention	= _t('Check your email and follow the instructions. If you do not receive the message, check your SPAM settings on your account. Make sure your email account does not automatically delete SPAM.');
				break;

			case 'reset-pwd':
				$title		= _t('Message sent successfully!');
				$message	= _t('A confirmation message to manage a new password has been sent to your email.');
				break;

			case 'savepwd':
				$title		= _t('Password saved successfully.');
				$message	= _t('The new password has been saved correctly and the session has been started automatically.');
				break;

			case 'reset-act':
				$title   = _t('Message sent successfully!');
				$message = _t('The activation message has been successfully sent to your email account.');
				break;

			default:
				redirect();
		}

		return [
			'title'		=> $title,
			'message'	=> $message,
			'attention' => $attention ?? '',
			'options'	=> config('usys.options'),
		];
	}

	/**
	 * Get
	 */
	protected function getUserFromToken($token): ?array
	{
		$user = $this->db->safeFind("
		SELECT
		 fullname ,
		 username ,
		 email ,
		 status
		FROM `#__users`
		WHERE id = ?", $token->user_id)->fetch();

		// security
		if ($user && $user['status'] === 'autosignup') {
			return $user;
		}

		$token->destroy();
		return null;
	}

	/**
	 * Get
	 */
	protected function getUserFromSession(): ?array
	{
		return null;
		//$user_id = curuser()->getPossibleUserId();

		if (!$user_id) {
			return null;
		}

		return $this->db->safeFind("
		SELECT
		 username AS email_username,
		 fullname 
		FROM `#__users`
		WHERE id = ?", $user_id)->fetch() ?: null;
	}
}
