<?php

    require_once("scripts/cmdline.php");
    require_once("vendors/jsmin.php");

    $src = file_get_contents('_media/tiny_mce/themes/advanced/editor_template_src.js');
    echo strlen($src)." ";
    $compressed = JSMin::minify($src);
    file_put_contents('_media/tiny_mce/themes/advanced/editor_template.js', $compressed);
    echo strlen($compressed);