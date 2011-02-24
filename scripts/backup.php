<?php

    /*
     * Poor-man's backup service; encrypts a database dump and writes 
     * it to a directory that's synchronized with a dropbox account.
     */

    require_once("scripts/cmdline.php");
    require_once("engine/start.php");

    $now = date("YmdHi");
    $output = "/etc/dropbox/Dropbox/envaya/envaya$now.sql.gz.nc";
    $dump = "mysqldump envaya -u dropbox --password=''";
    $crypt = "mcrypt -q --key ".escapeshellarg(Config::get('dbpass'));

    echo system("$dump | gzip | $crypt > $output && chmod 644 $output");