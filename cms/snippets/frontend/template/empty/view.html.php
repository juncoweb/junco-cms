<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

?>
<!DOCTYPE html>
<html lang="<?php echo $this->getLang() ?>">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="robots" content="index, follow" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="author" content="<?php echo $this->site->author ?>" />
    <meta name="description" content="<?php echo $this->site->description ?>" />

    <!-- link -->
    <?= $this->renderLink() ?>
    <!-- css -->
    <?php echo $this->renderCss() ?>
    <!-- script -->
    <?php echo $this->renderHeadJs() ?>
    <script>
        let theme = localStorage.getItem('prefers-color-scheme') || 'auto';
        if (theme === 'auto') {
            theme = (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
        }
        document.documentElement.setAttribute('data-theme', theme);
    </script>
    <!-- title -->
    <title><?php echo $this->getDocumentTitle() ?></title>
</head>

<body>
    <?php echo $this->content ?>

    <!-- script -->
    <?php echo $this->renderJs() ?>

</body>

</html><!-- Site developed by <?php echo $this->site->author ?> for <?php echo $this->site->name ?>. -->