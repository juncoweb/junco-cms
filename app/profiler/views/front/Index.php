<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

if ($frame) {
    $frame = '<iframe src="' . $frame . '" height=""></iframe>';
}

?>
<!DOCTYPE html>
<html>

<head>
    <title><?= $title ?></title>
    <link type="text/css" rel="stylesheet" href="<?= $base_url ?>assets/profiler.min.css">
    <script type="text/javascript" src="<?= $base_url ?>assets/profiler.min.js"></script>
</head>

<body>
    <?= $frame ?>
    <console>
        <h1>
            <ul>
                <li><a href="javascript:void(0)" class="toggle"></a></li>
                <li><a href="javascript:void(0)" class="clear"></a></li>
            </ul><?= $title ?>
        </h1>
        <div></div>
    </console>
</body>

</html>
<?php

die; // This is necessary so that the console is not displayed again.

?>