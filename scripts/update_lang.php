<?php
    /*
     * Extracts a zip file containing interface translations (as generated from the "Export Translations" link)
     * into the current source tree. Overwrites any existing files with the same name.
     */

    $root = dirname(__DIR__);

    require_once "$root/scripts/cmdline.php";
    require_once "$root/start.php";

    $filename = $_SERVER['argv'][1];
    
    $zip = new ZipArchive();
    $zip->open($filename);
    $zip->extractTo($root);