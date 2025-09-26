<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;
use Junco\Contact\Notification\ContactNotification;
use Junco\Users\Notification\UserNotifiable;

class ContactModel extends Model
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
     * Status
     */
    public function status()
    {
        $data = $this->filter(POST, ['id' => 'id|array|required:abort']);

        // query
        $this->db->exec("UPDATE `#__contact` SET status = IF(status > 0, 0, 1) WHERE id IN (?..)", $data['id']);
    }

    /**
     * Take
     */
    public function take()
    {
        $data = $this->filter(POST, [
            'contact_name'    => 'text|required',
            'contact_email'   => 'email|required',
            'contact_message' => '',
        ]);

        if (!$this->verifyCaptcha()) {
            return $this->unprocessable(_t('The captcha has not been resolved correctly.'));
        }

        if (!$data['contact_message']) {
            return $this->unprocessable(_t('Please, fill in the message.'));
        }

        $data['user_ip'] = curuser()->getIpAsBinary();
        $data['user_id'] = curuser()->getId();

        // flood control
        if ($max = $this->exceededMax($data['user_ip'])) {
            $message = '<b>' . _t('Your message has not been sent.') . '</b> ';
            $message .= sprintf(_t('For safety, the site does not allow more than %d messages per hour.'), $max);

            return $this->unprocessable($message);
        }

        // query - insert
        $this->db->exec("INSERT INTO `#__contact` (??) VALUES (??)", $data);
        $data['contact_id'] = $this->db->lastInsertId();

        // notify
        UserNotifiable::notifyByLabel(L_SYSTEM_ADMIN, new ContactNotification($data));
    }

    /**
     * Delete
     */
    public function delete()
    {
        $data = $this->filter(POST, ['id' => 'id|array|required:abort']);

        // query
        $this->db->exec("DELETE FROM `#__contact` WHERE id IN (?..)", $data['id']);
    }

    /**
     * Verify
     */
    protected function verifyCaptcha(): bool
    {
        $captcha = config('contact.captcha');

        return $captcha
            ? (new FormCaptcha)->verify($captcha)
            : true;
    }

    /**
     * 
     */
    protected function exceededMax($user_ip): int
    {
        $max = (int)config('contact.floodcontrol');

        if (!$max) {
            return 0;
        }

        $total = $this->db->query("
        SELECT COUNT(*)
        FROM `#__contact`
        WHERE user_ip = ?
        AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)", $user_ip)->fetchColumn();

        return ($total >= $max)
            ? $max
            : 0;
    }
}
