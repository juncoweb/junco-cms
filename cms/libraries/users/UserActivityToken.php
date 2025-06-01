<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Users;

use Junco\Utils\Aleatory;
use Filter;
use Email;
use Exception;

/**
 * Token
 *
 * @abstract: manages the 'Token' security layer
 */
class UserActivityToken
{
    // const
    const SELECTOR_LENGTH = 12;
    const VALIDATOR_LENGTH = 20;

    // vars
    public int    $id       = 0;
    public int    $user_id  = 0;
    public string $to       = '';
    public string $value    = '';
    public string $key      = '';

    /**
     * Constructor
     */
    private function __construct(string &$token, array &$data)
    {
        $this->id      = $data['id'];
        $this->user_id = $data['user_id'];
        $this->to      = $data['token_to'];
        $this->value   = $token;
        $this->key     = config('users-activities.token_key');
    }

    /**
     * Create an instance of the class
     *
     * @param int    $method  The method with which to recover the token.
     * @param string $type    One type of token: activation, signup, savepwd, savemail, autologin
     * @param bool   $nte     Do not throw errors.
     *
     * @return object|bool
     */
    public static function get(int $method, string $type, bool $nte = false)
    {
        try {
            $token  = Filter::input($method, config('users-activities.token_key'));
            $length = self::SELECTOR_LENGTH + self::VALIDATOR_LENGTH;

            if (!$token || !preg_match('@^[\w-]{' . $length . '}$@i', $token)) {
                throw new Exception('', -1);
            }

            $selector  = substr($token, 0, self::SELECTOR_LENGTH);
            $validator = substr($token, -self::VALIDATOR_LENGTH);
            $expires   = 'FALSE';

            switch ($type) {
                case 'activation':
                    break;
                default:
                    $lifetime = config('users-activities.token_lifetime');
                    if ($lifetime > 0) {
                        $expires = "NOW() > DATE_ADD(a.created_at, INTERVAL $lifetime HOUR)";
                    }
                    break;
            }

            // query
            $data = db()->safeFind("
			SELECT
			 a.id ,
			 a.user_id ,
			 a.activity_type ,
			 ($expires) AS expires ,
			 t.token_validator ,
			 t.token_to ,
			 t.status
			FROM `#__users_activities_tokens` t
			LEFT JOIN `#__users_activities` a ON ( a.id = t.activity_id )
			WHERE t.token_selector = ?", $selector)->fetch();

            if (!$data) {
                throw new Exception('', -10);
            }

            if ($data['activity_type'] != $type) {
                throw new Exception('', -11);
            }

            if ($data['status']) {
                throw new Exception('', -12);
            }

            if ($data['expires']) {
                throw new Exception('', -13);
            }

            if (!hash_equals($data['token_validator'], hash('sha256', $validator))) {
                throw new Exception('', -14);
            }

            return new self($token, $data);
        } catch (Exception $e) {
            $code = $e->getCode();

            if (!($type === 'signup' && $code === -1)) {
                (new UserActivity)->record('token', $code, $data['user_id'] ?? 0, [
                    'activity_id' => $data['id'] ?? 0,
                    'type'        => $type,
                    'token'       => $token
                ]);
            }

            if ($nte) {
                return false;
            } else {
                throw new Exception(_t('The code used is invalid or has expired.'));
            }
        }
    }

    /**
     * Destroy the current token
     *
     * @return this
     */
    public function destroy()
    {
        db()->safeExec("UPDATE `#__users_activities_tokens` SET status = 1 WHERE activity_id = ?", $this->id);
        return $this;
    }

    /**
     * Generate a token
     *
     * @param string $type      One type of token.
     * @param int    $user_id   The user ID.
     * @param string $to        The user's email where the token will be sent.
     */
    public static function generate(string $type, int $user_id, string $to)
    {
        // vars
        $token        = Aleatory::token(self::SELECTOR_LENGTH + self::VALIDATOR_LENGTH);
        $selector    = substr($token, 0, self::SELECTOR_LENGTH);
        $validator    = hash('sha256', substr($token, -self::VALIDATOR_LENGTH));
        $db            = db();


        // query - I disable previous tokens 
        $db->safeExec("
		UPDATE `#__users_activities_tokens`
		SET status = -1
		WHERE status = 0
		AND activity_id IN (SELECT id FROM `#__users_activities` WHERE user_id = ? AND activity_type = ?)", $user_id, $type);

        // query - I record activity
        $db->safeExec("INSERT INTO `#__users_activities` (??) VALUES (??)", [
            'user_id'            => $user_id,
            'user_ip'            => curuser()->getIpAsBinary(),
            'activity_type'        => $type,
        ]);

        // query - save the token
        $db->safeExec("INSERT INTO `#__users_activities_tokens` (??) VALUES (??)", [
            'activity_id'        => $db->lastInsertId(),
            'token_selector'    => $selector,
            'token_validator'    => $validator,
            'token_to'            => $to
        ]);

        switch ($type) {
            case 'validation':
                return $token;

            case 'activation':
                return url('/usys.activation', [config('users-activities.token_key') => $token], true);

            case 'savepwd':
                return url('/usys.password/edit', [config('users-activities.token_key') => $token], true);

            case 'savemail':
                return url('/usys.email/save', [config('users-activities.token_key') => $token], true);

            default:
                return url("/usys/$type", [config('users-activities.token_key') => $token], true);
        }
    }

    /**
     * Generate a token and then send
     *
     * @param string $type      One type of token.
     * @param int    $user_id   The user ID.
     * @param string $to        The user's email where the token will be sent.
     * @param string $fullname  The user fullname.
     */
    public static function generateAndSend(string $type, int $user_id, string $to, string $fullname)
    {
        $code = self::generate($type, $user_id, $to);

        switch ($type) {
            case 'signup':
            case 'activation':
                $subject   = _t('User Account Activation');
                $paragraph = _t('To activate your account and begin using the website, click on the link below:');
                break;

            case 'savepwd':
                $subject   = _t('Reset Password');
                $paragraph = _t('To reset the password, click on the link:');
                break;

            case 'autologin':
                $site_name = config('site.name');
                $subject   = sprintf(_t('Help to re-enter on %s'), $site_name);
                $paragraph = sprintf(_t('We\'re sorry you had trouble getting into your %s account.'), $site_name);
                break;

            case 'savemail':
                $subject   = _t('New Email');
                $paragraph = _t('To validate the new email, follow the link below:');
                break;

            default:
                abort();
        }

        // message
        $message = Email::getMessage();
        $message->line(_t('Hello, %s'), $fullname);
        $message->line($paragraph);
        $message->codelink($code);
        $message->legal();

        // email
        $email = new Email;
        $email->to($to);
        $email->subject($subject);
        $email->message($message);

        return $email->send();
    }
}
