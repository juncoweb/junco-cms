<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

defined('IS_TEST') or die;

// examples
$url = url('admin/samples/show', ['key' => 'modal.demo.modal']);
$table = snippet('sample_table', 'samples');
$rows = [
	[
		'code' =>
		// 1
		"Modal({
		size: 'large',
		draggable: 0,
		overlay: 1,
		destroy: 1,
		title: 'Modal test',
		content: 'Hello world!',
		//onClose: function() { return confirm('cancel?'); },
		buttons:[{type:'close',caption:'Close'}],
	});",
		'details' => '',
	],
	[
		'code' =>
		// 2
		"var target = Modal({
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
	});",
		'details' => '',
	],

	[
		'code' =>
		// 3
		"JsRequest.modal({
		url: '$url',
		data: {modal:1},
		modalOptions: {
			//ID: 'example_3',
			size: 'medium',
			//draggable: 1,
			//overlay: 0,
			//destroy: 1,
		},
	});",
		'details' => '',
	],
];

// loop
foreach ($rows as $i => $row) {
	$table->row($i + 1, $row['code'], $row['details']);
}

// template
$tpl = Template::get();
$tpl->title('Modal');
$tpl->content .= $table->render();

return $tpl->response();
