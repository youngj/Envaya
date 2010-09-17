<?php

    require_once("scripts/cmdline.php");
    require_once("engine/start.php");

    $now = date("YmdHi");

    echo system("mysqldump envaya -u dropbox --password='' | gzip > /etc/dropbox/Dropbox/envaya/envaya$now.sql.gz");