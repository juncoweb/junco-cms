<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

// form
$form = Form::get('', 'language-form');
$form->setValues($values);
$form->select('lang', $languages)->setLabel(_t('Select'));
$form->separate(_t('The site in your language'));

return $form->render();
