<?php

/**
 * Filesystem
 */

return [
	/**
	 * General
	 */
	'default_rename' => '4',

	/**
	 * Images
	 */
	'accept_images' => [
		0 => 'jpg',
		1 => 'jpeg',
		2 => 'gif',
		3 => 'png',
		4 => 'webp'
	],
	'image_resizes' => [
		'proportional' => [
			'name' => 'Proportional',
			'mode' => 1,
			'sizes' => [
				'thumb' => '160',
				'small' => '320',
				'medium' => '640',
				'large' => '1280',
				'x-large' => '1920'
			]
		],
		'proportional_area' => [
			'name' => 'Proportional Area',
			'mode' => 2,
			'sizes' => [
				'thumb' => '160',
				'small' => '320',
				'medium' => '640',
				'large' => '1280',
				'x-large' => '1920'
			]
		],
		'square' => [
			'name' => 'Square (1:1)',
			'mode' => 4,
			'sizes' => [
				'thumb' => '160x160',
				'small' => '320x320',
				'medium' => '640x640',
				'large' => '1280x1280',
				'x-large' => '1920x1920'
			]
		],
		'4:3' => [
			'name' => 'Classic (4:3)',
			'mode' => 4,
			'sizes' => [
				'thumb' => '160x120',
				'small' => '320x240',
				'medium' => '640x480',
				'large' => '1280x960',
				'x-large' => '1920x1440'
			]
		],
		'3:2' => [
			'name' => 'Modern (3:2)',
			'mode' => 4,
			'sizes' => [
				'thumb' => '150x100',
				'small' => '300x200',
				'medium' => '600x400',
				'large' => '1200x800',
				'x-large' => '1920x1280'
			]
		],
		'16:9' => [
			'name' => 'Panoramic (16:9)',
			'mode' => 4,
			'sizes' => [
				'thumb' => '160x90',
				'small' => '320x180',
				'medium' => '640x360',
				'large' => '1280x720',
				'x-large' => '1920x1080'
			]
		],
		'1.91:1' => [
			'name' => 'OG Image (~1.91:1)',
			'mode' => 4,
			'sizes' => [
				'thumb' => '150x79',
				'small' => '300x157',
				'medium' => '600x315',
				'large' => '1200x630',
				'x-large' => '1920x1005'
			]
		],
		'3:1' => [
			'name' => 'Banner (3:1)',
			'mode' => 4,
			'sizes' => [
				'thumb' => '150x50',
				'small' => '300x100',
				'medium' => '600x200',
				'large' => '1200x400',
				'x-large' => '1920x640'
			]
		]
	],
	'jpeg_quality' => -1,
	'png_quality' => -1,
	'webp_quality' => -1,
];
