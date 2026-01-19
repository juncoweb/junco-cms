<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Template\WidgetInterface;

return function (WidgetInterface $widget) {
    $config = config('contact-widget');

    if ($config['contact-widget.links']) {
        $config['contact-widget.load_resources'] and app('assets')->css(['assets/contact-widget.min.css']);

        $html = '';
        foreach ($config['contact-widget.links'] as $row) {
            $html .= '<a href="' . $row['url'] . '"' . (empty($row['target']) ? '' : ' target="' . $row['target'] . '"')  . ' title="' . $row['title'] . '">'
                . '<i class="' . $row['icon'] . '"' . (empty($row['color']) ? '' : ' style="color: ' . $row['color'] . ';"') . '></i>'
                . '</a>';
        }

        $widget->section([
            'content' => '<div class="ci-links">' . $html . '</div>',
            'css' => 'widget-contact'
        ]);
    }
};
