<?php

/*
 * Minifies javascript files. 
 * Any other necessary compilation steps should be added here as needed.
 */     

require_once("scripts/cmdline.php");

function minify($srcFile, $destFile)
{
    $src = file_get_contents($srcFile);
    
    system("java -jar vendors/yuicompressor-2.4.2.jar --type js -o ".escapeshellarg($destFile). " ".escapeshellarg($srcFile));
    
    $compressed = file_get_contents($destFile);

    echo strlen($src)." ".strlen($compressed)." $destFile\n";
    
}

minify('_media/tiny_mce/themes/advanced/editor_template_src.js',
        '_media/tiny_mce/themes/advanced/editor_template.js');

minify('_media/swfupload_src.js',
        '_media/swfupload.js');
        
minify('views/default/js/header_src.php', 'views/default/js/header.php');

minify('views/default/home/slideshow_src.php', 'views/default/home/slideshow.php');