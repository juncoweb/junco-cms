<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Form\FormElement;

use FormCaptcha;

class Enter extends FormElement
{
    /**
     * Constructor
     */
    public function __construct(string $label, array $attr = [])
    {
        $captcha = $this->extract($attr, 'captcha');

        if ($captcha) {
            $attr = array_merge($attr, (new FormCaptcha)->get($captcha));
        }

        $this->content = $this->extract($attr, 'html')
            . '<button' . $this->attr([
                'type'  => 'submit',
                'class' => 'btn btn-primary btn-solid'
            ], $attr) . '>'
            . ($label ?: _t('Enter'))
            . '</button>';
    }
}
