<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

?>
<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="robots" content="noindex, nofollow" />
    <meta name="author" content="<?php echo $this->site->author ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title><?php echo $this->site->name ?></title>
    <style>
        body {
            background-color: #FDFDFD;
        }

        #wrapper {
            margin: 20px auto;
            width: 90%;
        }

        .site {
            font: 10px arial;
            color: gray;
            text-align: right;
        }

        .site a,
        .site a:hover {
            color: gray;
            text-decoration: none;
        }

        .box {
            text-align: center;
            background-color: #fff;
            padding: 5px;
            border: 1px solid #B0C0CF;
        }

        .message {
            font: bold 14px Verdana;
        }

        .back {
            margin: 10px 0;
        }
    </style>
</head>

<body>
    <div id="wrapper">
        <div class="site"><a href="<?php echo $this->site->url ?>"><?php echo $this->site->name ?></a></div>
        <div class="box">
            <div class="message"><?php echo $this->content ?></div>
        </div>
    </div>
</body>

</html>