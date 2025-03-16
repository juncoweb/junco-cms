<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

$title = $this->getTitle() ?: 'Samples';
$this->css('cms/scripts/samples/css/template-default.css');
//$this->js('cms/scripts/samples/js/template-default.js');


?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
	<!-- css -->
	<?php echo $this->renderCss() ?>

	<!-- js -->
	<?php echo $this->renderJs() ?>

	<!-- title -->
	<title><?php echo $title ?></title>
</head>

<body>
	<h1><?php echo $title ?></h1>
	<div id="container">
		<?php echo $this->content ?>
	</div><!-- End Container -->
</body>

</html>