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

$tinymce_temp = "scripts/tinymce.tmp.js";

copy('_media/tiny_mce/tiny_mce_src.js', $tinymce_temp);
file_put_contents($tinymce_temp, file_get_contents('_media/tiny_mce/themes/advanced/editor_template_src.js'), FILE_APPEND);
file_put_contents($tinymce_temp, file_get_contents('_media/tiny_mce/plugins/paste/editor_plugin_src.js'), FILE_APPEND);
minify($tinymce_temp, '_media/tiny_mce/tiny_mce.js');
unlink($tinymce_temp);

minify('_media/swfupload_src.js', '_media/swfupload.js');

$js_src_files = glob('_media/inline_js/*_src.js');

foreach ($js_src_files as $js_src_file)
{
    minify($js_src_file,  str_replace('_src','',$js_src_file));
}
