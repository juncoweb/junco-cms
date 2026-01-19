<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

// css
$this->css('cms/scripts/install/css/install.css');


?>
<!DOCTYPE html>
<html lang="<?php echo $this->getLang() ?>">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="robots" content="noindex, nofollow" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <!-- css -->
    <?php echo $this->renderCss() ?>

    <!-- title -->
    <title><?php echo $this->site->name . ' - ' . _t('Installer'); ?></title>
</head>

<body>
    <div class="minMax">
        <div class="wrapper">

            <header class="header"><b><?php echo $this->site->name ?></b> | <?php echo _t('Installer') ?></header><!-- end header -->

            <?php echo $this->wizard() ?>

            <div class="relative">
                <nav class="nav">
                    <div>
                        <div>
                            <h1><?php echo $this->title ?></h1>
                        </div>
                        <div><?php echo $this->navbar() ?></div>
                    </div>
                </nav><!-- end title -->

                <main class="main">
                    <div id="notify" class="notify-box" role="alert"></div>
                    <?php echo $this->content ?>
                </main><!-- end content -->
                <div id="js-loading" class="install-loading noselect" style="display: none;">
                    <div><?php echo _t('Loading') ?></div>
                </div>
            </div>
        </div><!-- end wrapper -->

        <footer footer="footer">
            <p><?php echo sprintf('© %d by %s - All rights reserved', date('Y'), $this->site->name) ?></p>
        </footer><!-- end footer -->
    </div><!-- end minMax -->
    <!-- script -->
    <?php echo $this->renderJs() ?>
</body>

</html>