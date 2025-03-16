<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

// vars
$this->css(['assets/frontend-print.min.css']);
//$this->js(['assets/frontend-print.min.js']);



?>
<!DOCTYPE html>
<html lang="<?= $this->getLang() ?>">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta name="robots" content="index, follow" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<meta name="author" content="<?= $this->site->author ?>" />
	<meta name="description" content="<?= $this->site->description ?>" />

	<!-- link -->
	<link rel="shortcut icon" type="image/x-icon" href="<?= (isset($this->options->favicon) ? $this->options->favicon : 'favicon.ico') ?>" />
	<!-- css -->
	<?= $this->renderCss() ?>
	<!-- script -->
	<?= $this->renderHeadJs() ?>
	<!-- title -->
	<title><?= $this->getDocumentTitle() ?></title>
</head>

<body>
	<div class="do-not-print tpl-header"><a href="javascript:print()"><?= _t('Print') ?></a></div>
	<div class="tpl-container">
		<?= $this->content ?>
	</div>

	<!-- script -->
	<?= $this->renderJs() ?>

</body>

</html><!-- Site developed by <?= $this->site->author ?> for <?= $this->site->name ?>. -->