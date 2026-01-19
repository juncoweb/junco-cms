<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

return function (): string {
    if (cookie('cookieConsent')) {
        return '';
    }

    $legend = sprintf(
        _t('We use our own and third party cookies to improve the user experience through their navigation. If you continue to browse you accept their use. %sTerms and conditions of use%s'),
        '<a href="' . config('frontend.terms_url') . '" target="_blank">',
        '</a>'
    );

    return '<section id="cookieconsent" class="container cookie-consent visible" role="dialog" aria-live="polite" aria-describedby="cc-text cc-btn"><div>'
        .  '<p id="cc-text">' . $legend . '</p>'
        .  '<button id="cc-btn" class="btn btn-small btn-primary btn-solid">' . _t('Understood') . '</button>'
        . '</div></section>' . "\n";
};
