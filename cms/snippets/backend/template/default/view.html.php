<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

// vars
$this->css(['assets/backend.min.css']);
$this->js(['assets/backend.min.js']);

$html_title = '';
if ($this->title) {
    $icon = $this->title_options['icon'] ?? '';
    if (!$icon) {
        $icon = 'fa-solid fa-file';
    } elseif ($icon === -1) {
        $icon = 'fa-solid fa-triangle-exclamation';
    }

    $html_title = '<div class="layout-title"><h1>' . $this->getTitle() . '</h1></div>';

    if ($this->help_url) {
        $html_title .= '<div><a href="' . $this->help_url . '" target="_blank" title="' . ($t = _t('Help')) . '">'
            . '<i class="fa-solid fa-circle-question" aria-hidden="true"></i>'
            . '<span class="visually-hidden">' . $t . '</span>'
            . '</a></div>';
    }

    $html_title = '<div class="layout-title-group">'
        . '<div class="layout-image" aria-hidden="true"><i class="' . $icon . '"></i></div>'
        . $html_title
        . '</div>';
}

// mainbar
$mainbar = '';
if (!empty($this->options->mainbar)) {
    $mainbar = $this->getWidget(
        $this->options->mainbar,
        $this->options->mainbar_widget ?? 'backend'
    );
}

// sidebar
$sidebar = '';
if (!empty($this->options->sidebar)) {
    $sidebar = '<aside class="layout-aside"><div>'
        . $this->getWidget(
            $this->options->sidebar,
            $this->options->sidebar_widget ?? 'backend'
        )
        . '<div class="navbar-minimizer"><a href="javascript:void(0)" role="button" title="' . _t('Expand menu') . '">'
        .   '<i class="fa-solid fa-chevron-left" aria-hidden="true"></i>'
        .  '</a></div>'
        . '</div></aside><!-- end aside -->';
}

// thirdbar
$thirdbar = '';
if (!empty($this->options->thirdbar)) {
    $thirdbar = '<div class="layout-thirdbar">'
        . $this->getWidget(
            $this->options->thirdbar,
            $this->options->thirdbar_widget ?? 'backend'
        )
        . '</div><!-- end thirdbar -->';
}

//
$minimized = $this->isMinimized ? ' class="navbar-minimized"' : '';
$footer    = sprintf(_t('© %d by %s - All rights reserved'), date('Y'), '<a href="' . $this->site->url . '" class="site">' . $this->site->name . '</a>');

?>
<!DOCTYPE html>
<html lang="<?= $this->getLang() ?>">

<head>
    <meta charset="UTF-8">
    <meta name="robots" content="noindex, nofollow">
    <meta name="author" content="<?= $this->site->author ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- link -->
    <link rel="shortcut icon" type="image/x-icon" href="<?= $this->site->baseurl ?>favicon.ico" />
    <!-- css -->
    <?= $this->renderCss() ?>
    <!-- title -->
    <title><?= $this->getDocumentTitle() ?></title>
    <script>
        let theme = localStorage.getItem('prefers-color-scheme') || 'auto';
        if (theme === 'auto') {
            theme = (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
        }
        document.documentElement.setAttribute('data-theme', theme);
    </script>
</head>
<body<?= $minimized ?>>
    <div class="accessibility-nav">
        <a href="#content" class="visually-hidden-focusable"><?= _t('Skip to main content') ?></a>
        <a href="#sidebar" class="visually-hidden-focusable"><?= _t('Skip to sidebar') ?></a>
    </div>

    <header class="layout-header<?= ($this->themeColor === 'default' ? '' : ' header-' . $this->themeColor) ?>" data-tck="<?= $this->themeColorKey ?>" data-sticky>
        <div>
            <div class="layout-logo">
                <a href="<?= url('admin/') ?>">
                    <div class="layout-capital" aria-hidden="true"><?= $this->getCapital() ?></div>
                    <div class="layout-sitename" aria-hidden="true"><span><?= $this->site->name ?></span><span> | <?= _t('Administration') ?></span></div>
                    <span class="visually-hidden"><?= $this->site->name ?></span><span>
                </a>
            </div>
            <?= $mainbar ?>
        </div>
    </header><!-- end header -->

    <?= $sidebar ?>

    <div class="layout-main">
        <?= $thirdbar ?>
        <main id="content" class="layout-content">
            <?= $html_title ?>
            <?= $this->content ?>
        </main><!-- end content -->
        <footer class="layout-footer">
            <div class="content"><?= $footer ?></div>
        </footer><!-- end footer -->
    </div><!-- end main -->
    <!-- script -->
    <?= $this->renderJs() ?>
    </body>

</html>