<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

return function (&$portal) {
    $domready = 'JsForm('
        . '"#contact-form", '
        . '{focusable:false}).request(JsUrl("/contact/take"), function(message, code) { '
        .    'if (code) { window.location = JsUrl("/contact/message"); } else { alert(message); }'
        . '})';

    config('contact-widget.load_resources')
        and app('assets')->css(['assets/contact-widget.min.css']);
    app('assets')->domready($domready);

    $curuser = curuser();
    $html = '<div id="contact">'
        . '<form id="contact-form" class="contact-form">'
        .   '<p><input type="text" name="contact_name" value="' . $curuser->fullname . '" class="input-field input-large" placeholder="' . _t('Name') . '" required /></p>'
        .   '<p><input type="email" name="contact_email" value="' . $curuser->email . '" class="input-field input-large" placeholder="' . _t('Email') . '" required /></p>'
        .   '<p><textarea placeholder="Mensaje" name="contact_message" class="input-field input-large" control-felem="auto-grow" data-min-height="84px" required></textarea></p>'
        .   '<button type="submit" class="btn btn-primary btn-solid btn-large">' . _t('Enter') . '</button>'
        . FormSecurity::getToken()
        . '</form>'
        . '</div>';

    # portal
    $portal->section([
        'title' => _t('Contact'),
        'content' => $html,
        'css' => 'widget-contact-form'
    ]);
};
