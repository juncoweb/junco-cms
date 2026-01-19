<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

defined('IS_TEST') or die;

// vars
//$colors = ['default', 'primary','secondary','success','info','warning','danger'];
$colors = ['default'];
$html = '';
$domready = '';
$lorem_1 = 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt. Cras dapibus. Vivamus elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus. Phasellus viverra nulla ut metus varius laoreet. Quisque rutrum. Aenean imperdiet.';
$lorem_2 = 'Etiam rhoncus. Maecenas tempus, tellus eget condimentum rhoncus, sem quam semper libero, sit amet adipiscing sem neque sed ipsum.';

foreach ($colors as $i => $color) {
    $tab_id = 't' . $i;
    //
    $tabs = Tabs::get('', $tab_id);
    $tabs->tab('One', $lorem_1);
    $tabs->tab('Two', $lorem_2);
    $tabs->tab('Three');

    $html .= $tabs->render();
    $domready .= 'JsTabs("#' . $tab_id . '").select();';
}

$html = '<div style="margin: 50px;">' . $html . '</div>';

// template
$tpl = Template::get();
$tpl->options([
    'domready' => $domready,
    'thirdbar' => 'system.thirdbar'
]);
$tpl->content($html);

return $tpl->response();
