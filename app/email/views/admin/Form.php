<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

// form
$form = Form::get();

// actions
$fac = $form->getActions();
$fac->enter();
$fac->cancel();

// elements
$form->input('email_to', ['placeholder' => _t('To'), 'class' => 'input-large']);
$form->input('email_subject', ['placeholder' => _t('Subject'), 'class' => 'input-large']);
$form->editor('email_message');

return $form->render();
