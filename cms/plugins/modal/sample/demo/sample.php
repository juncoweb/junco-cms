<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

defined('IS_TEST') or die;

// samples
$samples = Samples::get();
$url = url('admin/samples/show', ['key' => 'modal.demo.modal']);

// 1
$samples->js("Modal({
    size: 'large',
    draggable: 0,
    overlay: 1,
    destroy: 1,
    title: 'Modal test',
    content: 'Hello world!',
    //onClose: function() { return confirm('cancel?'); },
    buttons:[{type:'close',caption:'Close'}],
});")->setLabel('Basic modal');

// 2
$samples->js("var target = Modal({
    size: 'large',
    title: 'Modal test',
    content: 'Hello world!',
    buttons:[{type:'close',caption:'Close'}],
});

Modal({
    size: 'medium',
    target: target,
    title: 'Modal test',
    content: 'Hello world!',
    buttons:[{type:'close',caption:'Close'}],
});")->setLabel('Modal inside modal');

// 3
$samples->js("JsRequest.modal({
    url: '$url',
    data: {modal:1},
    modalOptions: {
        //ID: 'example_3',
        size: 'medium',
        //draggable: 1,
        //overlay: 0,
        //destroy: 1,
    },
});")->setLabel('Modal with ajax content');

$html = $samples->render();

// template
$tpl = Template::get();
$tpl->title('Modal');
$tpl->content($html);

return $tpl->response();
