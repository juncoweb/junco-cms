<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

class contact_master_default_snippet
{
    public function render()
    {
        $html = '<p>' . sprintf(
            _t('Use the form below to contact the site administrator. If you prefer to use your mail, our address is %s'),
            '<span class="contact-email">' . config('site.email') . '</span>'
        ) . '</p>';

        $curuser = curuser();
        $felem   = Form::getElements();
        if ($curuser->id) {
            $felem->setValues([
                'contact_name' => $curuser->fullname,
                'contact_email' => $curuser->email,
            ]);
        }

        $html .= '<div class="contact" id="contact">'
            . '<div class="contact-success color-green">' . _t('The message has been sent successfully.') . '<p><button class="btn btn-primary btn-solid">' . _t('Back') . '</button></p></div>'
            . '<form id="js-form">'
            .   '<div id="msg-w" class="notify-box"></div>'
            .    '<div class="contact-form">
						<p>' . $felem->input('contact_name', ['placeholder' => _t('Name'), 'required' => '']) . '</p>
						<p>' . $felem->input('contact_email', ['placeholder' => _t('Email'), 'required' => '']) . '</p>
						<p>' . $felem->textarea('contact_message', ['placeholder' => _t('Message'), 'required' => '', 'auto-grow' => '', 'data-min-height' => '84px']) . '</p>
						<p>' . $felem->enter(_t('Enter'), ['captcha' => config('contact.captcha')]) . '</p>
				</div>'
            . FormSecurity::getToken()
            . '</form>'
            . '</div>';

        return $html;
    }
}
