<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

defined('IS_TEST') or die;

// vars
$example = Filter::input(POST, 'example', 'id');
$string = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas ac vestibulum nunc. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Maecenas vitae lacus mauris. Etiam sed rhoncus lacus. Aliquam laoreet sem at dui eleifend nec pellentesque orci sagittis. Praesent eu ullamcorper magna. Phasellus eget massa a nibh pretium sodales. Ut a vehicula lectus. Aliquam erat volutpat. Integer facilisis, velit sit amet viverra interdum, libero magna hendrerit purus, eget tincidunt lacus lectus at magna. Praesent imperdiet pellentesque nibh, vitae molestie massa rhoncus nec. Nullam sapien leo, sollicitudin ut accumsan et, cursus et leo. Aenean nunc dolor, sollicitudin vel vehicula id, sollicitudin eget justo. Duis consequat leo eu dui hendrerit ultrices.';

// modal
$modal = Modal::get();
$modal->close();
$modal->title('Hello!');

$html = '<div>time: ' . time() . '</div>';
switch ($example) {
    case 1:
        $modal->button('alert(\'Hello!\')', _t('Enter'));
        break;
    case 2:
        $html .= str_repeat($string, 0);
        break;
}

$modal->content($html);

return $modal->response();
