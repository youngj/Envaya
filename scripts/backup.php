<?php

    require_once("scripts/cmdline.php");
    require_once("engine/start.php");

    global $CONFIG;
    $now = date("YmdHi");
    $output = "/etc/dropbox/Dropbox/envaya/envaya$now.sql.gz.nc";
    $dump = "mysqldump envaya -u dropbox --password=''";
    $crypt = "mcrypt -q --key '{$CONFIG->dbpass}'";

    echo system("$dump | gzip | $crypt > $output && chmod 644 $output");