<?php

/*
 * Minifies javascript files. 
 * Any other necessary compilation steps should be added here as needed.
 */     

require_once("scripts/cmdline.php");
require_once("vendors/jsmin.php");

function minify($srcFile, $destFile)
{
    $src = file_get_contents($srcFile);

    $compressed = JSMin::minify($src);
    file_put_contents($destFile, $compressed);

    echo strlen($src)." ".strlen($compressed)." $destFile\n";
}

minify('_media/tiny_mce/themes/advanced/editor_template_src.js',
        '_media/tiny_mce/themes/advanced/editor_template.js');

minify('views/default/js/header_src.php', 'views/default/js/header.php');