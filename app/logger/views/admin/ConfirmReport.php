<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */


if ($total) {
    $question = sprintf(
        _nt('Are you sure you want to report the bug?', 'Are you sure you want to report %d bugs?', $total),
        $total
    );
} else {
    $question = _t('Are you sure you want to report all bugs?');
}

// form
$form = Form::get();
$form->setValues($values);
$form->hidden('id');
//
$form->element($question);
$form->textarea('message', ['auto-grow' => ''])->setLabel(_t('Message'));

$html = $form->render();
$html .= '<div class="dialog dialog-warning">' . _t('By submitting the report, you agree to share personal information.') . '</div>';


// modal
$modal = Modal::get();
$modal->close();
$modal->enter();
$modal->title(_t('Report bugs'), 'fa-solid fa-bug');
$modal->content($html);

return $modal->response();
