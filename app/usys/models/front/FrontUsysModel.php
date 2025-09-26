<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;
use Junco\Users\Enum\ActivityType;
use Junco\Users\Enum\UserStatus;
use Junco\Users\UserActivityToken;
use Junco\Usys\LoginWidgetCollector;

class FrontUsysModel extends Model
{
    // vars
    protected $db;

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
            'options'       => config('usys.options'),
            'login_plugins' => config('usys.login_plugins'),
            'legal_url'     => config('users.legal'),
            'user'          => null,
            'widgets'       => []
        ];

        // security
        if (!config('users.default_ucid')) {
            return $data + ['error' => true];
        }

        $input = $this->filter(GET, ['token' => 'text']);

        $token = UserActivityToken::from($input['token'], ActivityType::signup);

        if ($token) {
            $user = $this->getUserFromToken($token);

            if ($user) {
                $data['user']          = $user;
                $data['token']         = $input['token'];
                $data['login_plugins'] = '';
            }
        }

        if ($data['legal_url']) {
            $data['legal_url'] = $this->getUrl($data['legal_url']);
        }

        if ($data['login_plugins']) {
            $data['widgets'] = (new LoginWidgetCollector)->getAll($data['login_plugins']);
        }

        return $data;
    }

    /**
     * Get
     */
    public function getResolveData()
    {
        $input = $this->filter(GET, ['redirect' => 'text']);

        //
        $user_id = auth()->getDeferredUserId();

        if (!$user_id) {
            return redirect(
                url('/usys/login', array_filter(['redirect' => $input['redirect']]))
            );
        }

        $mfa_url = $this->getNextUrl($user_id, $input['redirect']);

        if ($mfa_url) {
            return redirect($mfa_url);
        }

        auth()->execDeferredLogin();
        return redirect($input['redirect'], true);
    }

    /**
     * Get
     */
    public function getLoginData()
    {
        $input = $this->filter(GET, ['redirect' => 'text']);

        $data = $input + [
            'user_id'     => auth()->getDeferredUserId(),
            'options'     => config('usys.login_options'),
            'remember'    => config('users.remember'),
            'user'        => $this->getUserFromSession(),
            'widgets'     => $this->getWidgets()
        ];

        if ($data['user_id']) {
            $data['resolve_url'] = url('/usys/resolve');
        }

        return $data;
    }

    /**
     * Get
     */
    public function getAutologinData()
    {
        $input = $this->filter(GET, ['token' => 'text']);

        try {
            $token = UserActivityToken::from($input['token'], ActivityType::autologin);

            if (!$token) {
                throw new Exception(_t('The code used is invalid or has expired.'));
            }
            $token->destroy();

            auth()->login($token->getUserId());
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
        $input = $this->filter(GET, ['op' => '']);

        switch ($input['op']) {
            case 'signup':
                $title     = _t('Registration complete!');
                $message   = _t('Your account has been successfully created and an activation message has been sent to your email.');
                $attention = _t('Check your email and follow the instructions. If you do not receive the message, check your SPAM settings on your account. Make sure your email account does not automatically delete SPAM.');
                break;

            case 'login':
                $title     = _t('Account activation pending');
                $message   = _t('Your user account is not active yet.') . '<br />' . sprintf(_t('Remember that, if necessary, you can request a new %sactivation message%s.'), '<a href="' . url('/usys.activation/reset') . '">', '</a>');
                $attention = _t('Check your email and follow the instructions. If you do not receive the message, check your SPAM settings on your account. Make sure your email account does not automatically delete SPAM.');
                break;

            case 'reset-pwd':
                $title   = _t('Message sent successfully!');
                $message = _t('A confirmation message to manage a new password has been sent to your email.');
                break;

            case 'savepwd':
                $title   = _t('Password saved successfully.');
                $message = _t('The new password has been saved correctly and the session has been started automatically.');
                break;

            case 'reset-act':
                $title   = _t('Message sent successfully!');
                $message = _t('The activation message has been successfully sent to your email account.');
                break;

            default:
                redirect();
        }

        return [
            'title'     => $title,
            'message'   => $message,
            'attention' => $attention ?? '',
            'options'   => config('usys.options'),
        ];
    }

    /**
     * Get
     */
    protected function getWidgets(): array
    {
        $plugins = config('usys.login_plugins');

        return $plugins
            ? (new LoginWidgetCollector)->getAll($plugins)
            : [];
    }

    /**
     * Get
     */
    protected function getUserFromToken(UserActivityToken $token): ?array
    {
        $user = $this->db->query("
		SELECT
		 fullname ,
		 username ,
		 email ,
		 status
		FROM `#__users`
		WHERE id = ?", $token->getUserId())->fetch();

        // security
        if ($user && UserStatus::autosignup->isEqual($user['status'])) {
            return $user;
        }

        $token->destroy();
        return null;
    }

    /**
     * Get
     */
    protected function getUrl(string $url): string
    {
        if (strpos($url, ',') === false) {
            return $url;
        }

        $url     = str_replace(' ', '', $url);
        $partial = explode(',', $url, 2);
        $args    = [];

        empty($partial[1])
            or parse_str($partial[1], $args);

        return url($partial[0], $args, true);
    }

    /**
     * Get
     */
    protected function getUserFromSession(): ?array
    {
        return null;
        //$user_id = auth()->getDeferredUserId();

        if (!$user_id) {
            return null;
        }

        return $this->db->query("
		SELECT
		 username AS email_username,
		 fullname 
		FROM `#__users`
		WHERE id = ?", $user_id)->fetch() ?: null;
    }

    /**
     * Get
     * 
     * @ Is equal to UsysModel.php
     */
    protected function getNextUrl(int $user_id, string $redirect): string
    {
        $plugins = config('usys.mfa_plugins');

        foreach ($plugins as $plugin) {
            $url = Plugin::get('mfa', 'status', $plugin)?->run($user_id, $redirect);

            if ($url) {
                return $url;
            }
        }

        return '';
    }
}
