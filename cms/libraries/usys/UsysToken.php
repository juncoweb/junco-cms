<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Usys;

use Junco\Users\Enum\ActivityType;
use Junco\Users\UserActivityToken;
use Email;

class UsysToken
{
    /**
     * Send
     *
     * @param UserActivityToken  $token
     * @param string     $fullname  The user fullname.
     * 
     * @return bool
     */
    public function send(UserActivityToken $token, string $fullname): bool
    {
        switch ($token->getType()) {
            case ActivityType::signup:
            case ActivityType::activation:
                $subject   = _t('User Account Activation');
                $paragraph = _t('To activate your account and begin using the website, click on the link below:');
                break;

            case ActivityType::savepwd:
                $subject   = _t('Reset Password');
                $paragraph = _t('To reset the password, click on the link:');
                break;

            case ActivityType::autologin:
                $site_name = config('site.name');
                $subject   = sprintf(_t('Help to re-enter on %s'), $site_name);
                $paragraph = sprintf(_t('We are sorry you had trouble getting into your %s account.'), $site_name);
                break;

            case ActivityType::savemail:
                $subject   = _t('New Email');
                $paragraph = _t('To validate the new email, follow the link below:');
                break;

            default:
                return false;
        }

        // message
        $message = Email::getMessage();
        $message->line(_t('Hello, %s'), $fullname);
        $message->line($paragraph);
        $message->codelink($this->getUrl($token));
        $message->legal();

        // email
        $email = new Email;
        $email->to($token->getEmailTo());
        $email->subject($subject);
        $email->message($message);

        return $email->send();
    }

    /**
     * Get
     * 
     * @param UserActivityToken $token
     * 
     * @return string  the url with token
     */
    public function getUrl(UserActivityToken $token): ?string
    {
        return match ($token->getType()) {
            ActivityType::activation => url('/usys.activation', ['token' => $token->__toString()], true),
            ActivityType::savepwd    => url('/usys.password/edit', ['token' => $token->__toString()], true),
            ActivityType::savemail   => url('/usys.email/save', ['token' => $token->__toString()], true),
            ActivityType::signup     => url('/usys/signup', ['token' => $token->__toString()], true),
            ActivityType::login      => url('/usys/login', ['token' => $token->__toString()], true),
            ActivityType::autologin  => url('/usys/autologin', ['token' => $token->__toString()], true),
            default => null
        };
    }
}
