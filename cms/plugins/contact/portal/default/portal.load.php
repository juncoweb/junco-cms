<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Portal\PortalInterface;

return function (PortalInterface $portal) {
    $domready = 'JsForm("#portal-contact", {focusable:false})'
        . '.request(JsUrl("/contact/take"), function(res) { '
        .    'if (res.ok()) { window.location = JsUrl("/contact/message"); } else { alert(res.message); }'
        . '})';

    config('contact-widget.load_resources')
        and app('assets')->css(['assets/contact-widget.min.css']);
    app('assets')->domready($domready);

    $curuser = curuser();
    $felem   = Form::getElements();
    if ($curuser->getId()) {
        $felem->setValues([
            'contact_name' => $curuser->getName(),
            'contact_email' => $curuser->getEmail(),
        ]);
    }

    $html = '<div class="input-large btn-large">'
        . '<form id="portal-contact">'
        .   '<p>' . $felem->input('contact_name', ['placeholder' => $t = _t('Name'), 'aria-label' => $t, 'required' => '']) . '</p>'
        .   '<p>' . $felem->input('contact_email', ['placeholder' => $t = _t('Email'), 'aria-label' => $t, 'required' => '']) . '</p>'
        .   '<p>' . $felem->textarea('contact_message', ['placeholder' => $t = _t('Message'), 'aria-label' => $t, 'required' => '', 'auto-grow' => '', 'data-min-height' => '84px']) . '</p>'
        .   '<p>' . $felem->enter(_t('Enter'), ['captcha' => config('contact.captcha')]) . '</p>'
        .   FormSecurity::getToken()
        . '</form>'
        . '</div>';

    # portal
    $portal->section([
        'title'   => _t('Contact'),
        'content' => $html,
        'css'     => 'portal-contact',
        'attr'    => ['id' => 'contact']
    ]);
};
