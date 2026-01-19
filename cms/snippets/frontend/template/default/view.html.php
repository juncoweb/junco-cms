<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

// vars
$this->css(['assets/frontend.min.css']);
$this->js(['assets/frontend.min.js']);

// title
$title_html = '';
if (!empty($this->title) && ($this->options->show_title ?? true)) {
    $title_html = '<div class="tpl-title"><h1>' . $this->getTitle() . '</h1></div>';

    if ($this->help_url) {
        $title_html .= '<div class="tpl-help">'
            .  '<a href="' . $this->help_url . '" target="blank" title="' . ($t = _t('Help')) . '">'
            .    '<i aria-label="' . $t . '" class="fa-solid fa-circle-question"></i>'
            .  '</a>'
            . '</div>';
    }

    $pathway = $this->getPathway();
    $title_html = '<section class="tpl-title-container"><div class="container">'
        . ($pathway ? '<div class="pathway">' . $pathway . '</div>' : '')
        . '<div class="tpl-title-group">' . $title_html . '</div>'
        . '</div></section>' . "\n";
}

// navbar
$navbar_html = !empty($this->options->navbar)
    ? $this->getWidget(
        $this->options->navbar,
        $this->options->navbar_widget ?? 'frontend.navbar'
    )
    : '';

// after
if (!empty($this->options->after)) {
    $this->content .= $this->getWidget(
        $this->options->after,
        $this->options->after_widget ?? 'frontend.after'
    );
}

// content
$content_html = $this->content;
$this->content = ''; // freeing memory

if ($this->options->wrapped ?? true) {
    // sidebar
    $sidebar_html = !empty($this->options->sidebar)
        ? $this->getWidget(
            $this->options->sidebar,
            $this->options->sidebar_widget ?? 'frontend'
        )
        : '';

    $content_html = "\t\t" . '<main>' . "\n"
        . "\t\t" . $content_html . "\n"
        . "\t\t" . '</main><!-- end main -->' . "\n";

    if ($sidebar_html) {
        $content_html = "\t\t" . '<div class="main-wrapper">' . "\n"
            . "\t\t" . $content_html . "\n"
            . "\t\t" . '<aside id="sidebar">' . "\n"
            . "\t\t" . $sidebar_html . "\n"
            . "\t\t" . '</aside><!-- end sidebar -->' . "\n"
            . "\t\t" . '</div>' . "\n";
    }

    $content_html = '<section class="tpl-main">' . "\n"
        . "\t\t" . '<div class="container">' . "\n"
        . $content_html
        . "\t\t" . '</div>' . "\n"
        . "\t\t" . '</section><!-- end content -->' . "\n";
}

// bottom
$bottom_html = !empty($this->options->bottom)
    ? $this->getWidget(
        $this->options->bottom,
        $this->options->bottom_widget ?? 'frontend.bottom'
    )
    : '';

// footer
$footer_html = !empty($this->options->footer)
    ? "\n\t" . '<footer class="tpl-footer ' . $this->options->footer_style . '"><div class="container">' . $this->getWidget(
        $this->options->footer,
        $this->options->footer_widget ?? 'frontend.footer'
    ) . "\n\t" . '</div></footer>'
    : '';

?>
<!DOCTYPE html>
<html lang="<?= $this->getLang() ?>">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="robots" content="index, follow" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <meta name="author" content="<?= $this->site->author ?>" />
    <meta name="description" content="<?= $this->site->description ?>" />
    <?= $this->renderMeta() ?>
    <!-- link -->
    <?= $this->renderLink() ?>
    <!-- css -->
    <?= $this->renderCss() ?>
    <!-- script -->
    <?= $this->renderHeadJs() ?>
    <script>
        let theme = localStorage.getItem('prefers-color-scheme') || 'auto';
        if (theme === 'auto') {
            theme = (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
        }
        document.documentElement.setAttribute('data-theme', theme);
    </script>
    <!-- title -->
    <title><?= $this->getDocumentTitle() ?></title>
</head>
<body<?= $this->getBodyClass() ?>>
    <ul class="accessibility-nav">
        <li><a href="#content"><?= _t('Skip to main content') ?></a></li>
        <li><a href="#sidebar"><?= _t('Skip to sidebar') ?></a></li>
    </ul>
    <header class="tpl-header <?= $this->options->header_style ?>" data-sticky>
        <div class="top-header">
            <div class="container"><?= $this->renderTopHeader() ?></div>
        </div>
        <div class="container">
            <div class="main-header">
                <div class="logo">
                    <a href="<?= url() ?>" aria-label="<?= _t('Homepage') ?>"><?= $this->renderLogo() ?></a>
                </div>
                <div class="main-navbar">
                    <?= $navbar_html ?>
                    <?= $navbar_html ? '<a href="javascript:void(0)" role="button" class="pull-btn"><i class="fa-solid fa-bars"></i></a>' : '' ?>
                </div>
            </div>
        </div>
    </header>
    <div id="content"></div>
    <?= $title_html ?>
    <?= $content_html ?>
    <?= $bottom_html ?>
    <?= $footer_html ?>

    <section class="tpl-copyright <?= $this->options->copyright_style ?>">
        <div class="container">
            <?= $this->renderCopyright() ?>
        </div>
    </section>
    <?= $this->renderCookieConsent() ?>
    <!-- script -->
    <?= $this->renderJs() ?>

    </body>

</html><!-- Site developed by <?= $this->site->author ?> for <?= $this->site->name ?>. -->