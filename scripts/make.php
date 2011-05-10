<?php

/*
 * Minifies javascript files. 
 * Any other necessary compilation steps should be added here as needed.
 */     

require_once("scripts/cmdline.php");
require_once("engine/start.php");

function minify($srcFile, $destFile, $type='js')
{
    $src = file_get_contents($srcFile);
    
    system("java -jar vendors/yuicompressor-2.4.2.jar --type $type -o ".escapeshellarg($destFile). " ".escapeshellarg($srcFile));
    
    $compressed = file_get_contents($destFile);

    echo strlen($src)." ".strlen($compressed)." $destFile\n";  
}

class Build
{
    static function css()
    {
        $css_paths = glob("{views/default/css/*.php,mod/*/views/default/css/*.php}", GLOB_BRACE);

        foreach ($css_paths as $css_path)
        {
            $pathinfo = pathinfo($css_path);
            $filename = $pathinfo['filename'];
            $css_temp = "scripts/$filename.tmp.css";
            $raw_css = view("css/$filename");
            
            if (preg_match('/http(s)?:[^\s\)\"\']*/', $raw_css, $matches))
            {
                throw new Exception("Absolute URL {$matches[0]} found in $css_path. In order to work on both dev/production without recompiling, CSS files must not contain absolute paths.");
            }
            
            file_put_contents($css_temp, $raw_css);
            minify($css_temp, "_css/$filename.css", 'css');
            unlink($css_temp);
        }
    }
     
    static function tinymce()
    { 
        $tinymce_temp = "scripts/tinymce.tmp.js";

        copy('_media/tiny_mce/tiny_mce_src.js', $tinymce_temp);
        file_put_contents($tinymce_temp, file_get_contents('_media/tiny_mce/themes/advanced/editor_template_src.js'), FILE_APPEND);
        file_put_contents($tinymce_temp, file_get_contents('_media/tiny_mce/plugins/paste/editor_plugin_src.js'), FILE_APPEND);
        minify($tinymce_temp, '_media/tiny_mce/tiny_mce.js');
        unlink($tinymce_temp);
    }

    static function swfupload()
    {    
        minify('_media/swfupload_src.js', '_media/swfupload.js');
    }

    static function inline_js($name = '*')
    {
        $js_src_files = glob("{_media/inline_js_src/$name.js,mod/*/_media/inline_js_src/$name.js}", GLOB_BRACE);

        foreach ($js_src_files as $js_src_file)
        {
            minify($js_src_file,  str_replace('_src','',$js_src_file));
        }
    }

    static function all()
    {
        Build::css();
        Build::tinymce();
        Build::swfupload();
        Build::inline_js();
    }
}

$target = @$argv[1] ?: 'all';
$arg = @$argv[2];
if (method_exists('Build', $target))
{
    if ($arg)
    {
        Build::$target($arg);
    }
    else
    {
        Build::$target();
    }
}
else
{
    echo "Build::$target is not defined\n";
}