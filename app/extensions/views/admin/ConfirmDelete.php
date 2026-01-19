<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

$count = count($id);
$message = sprintf(_nt('Are you sure you want to delete the selected item?', 'Are you sure you want to delete the %d selected items?', $count), $count);

//
$form = Form::get();
$form->setValues([
    'id'            => $id,
    'option[files]' => true,
    'option[data]'  => true,
    'option[db]'    => true,
]);
$form->hidden('id');
//
$form->header('<b>' . _t('Options') . '</b>', false);
$form->checkbox('option[files]')->setLabel(_t('Delete files'));
$form->checkbox('option[data]')->setLabel(_t('Delete data'));
$form->checkbox('option[db]')->setLabel('Delete DB');

$html = '<p>' . $message . '</p>';
$html .= $form->render();


// modal
$modal = Modal::get();
$modal->title($t = _t('Delete'), 'fa-solid fa-trash');
$modal->enter($t);
$modal->close();
$modal->content($html);

return $modal->response();
