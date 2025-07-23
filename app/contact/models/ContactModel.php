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
        // data
        $this->filter(POST, ['id' => 'id|array|required:abort']);

        // query
        $this->db->safeExec("UPDATE `#__contact` SET status = IF(status > 0, 0, 1) WHERE id IN (?..)", $this->data['id']);
    }

    /**
     * Take
     */
    public function take()
    {
        // data
        $this->filter(POST, [
            'contact_name'    => 'text|required',
            'contact_email'   => 'email|required',
            'contact_message' => '',
        ]);

        if (!$this->verifyCaptcha()) {
            throw new Exception(_t('The captcha has not been resolved correctly.'));
        }

        if (!$this->data['contact_message']) {
            throw new Exception(_t('Please, fill in the message.'));
        }

        $this->data['user_ip'] = curuser()->getIpAsBinary();
        $this->data['user_id'] = curuser()->id;

        // security
        $this->floodcontrol($this->data['user_ip']);

        // query - insert
        $this->db->safeExec("INSERT INTO `#__contact` (??) VALUES (??)", $this->data);
        $this->data['contact_id'] = $this->db->lastInsertId();

        // notify
        UserNotifiable::notifyByLabel(L_SYSTEM_ADMIN, new ContactNotification($this->data));
    }

    /**
     * Delete
     */
    public function delete()
    {
        // data
        $this->filter(POST, ['id' => 'id|array|required:abort']);

        // query
        $this->db->safeExec("DELETE FROM `#__contact` WHERE id IN (?..)", $this->data['id']);
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
     * Flodcontrol
     */
    protected function floodcontrol($user_ip)
    {
        $max = (int)config('contact.floodcontrol');

        if (!$max) {
            return;
        }

        $total = $this->db->safeFind("
        SELECT COUNT(*)
        FROM `#__contact`
        WHERE user_ip = ?
        AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)", $user_ip)->fetchColumn();

        if ($total >= $max) {
            throw new Exception(
                '<b>' . _t('Your message has not been sent.') . '</b>'
                    . ' ' . sprintf(_t('For safety, the site does not allow more than %d messages per hour.'), $max)
            );
        }
    }
}
