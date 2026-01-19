<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Form\FormElement\CustomElement;

return function (array $attr): CustomElement {
    return new CustomElement('', '--- selector not found ---');
};
