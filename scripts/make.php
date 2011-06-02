<?php

/*
 * Minifies javascript and CSS files. 
 * Any other necessary compilation steps should be added here as needed.
 */     

require_once "scripts/cmdline.php";

function minify($srcFile, $destFile, $type='js')
{
    $src = file_get_contents($srcFile);
    
    system("java -jar vendors/yuicompressor-2.4.2.jar --type $type -o ".escapeshellarg($destFile). " ".escapeshellarg($srcFile));
    
    $compressed = file_get_contents($destFile);

    echo strlen($src)." ".strlen($compressed)." $destFile\n";  
}

class Build
{
    private static function module_glob()
    {
        return "{".implode(',',Config::get('modules'))."}";
    }    
    
    static function clean()
    {
        @unlink("build/lib_cache.php");
        @unlink("build/path_cache.php");
        
        @unlink("_media/swfupload.js");
        @unlink("_media/tiny_mce/tiny_mce.js");
        
        foreach (glob('_media/css/*.css') as $css)
        {
            unlink($css);
        }
        
        foreach (glob('_media/inline_js/*.js') as $js)
        {
            unlink($js);
        }
    }
    
    static function lib_cache()
    {
        @unlink("build/lib_cache.php");
        
        require_once "start.php";
        $paths = Engine::get_lib_paths();  
        static::write_file("build/lib_cache.php", static::get_array_php($paths));
    }
    
    static function path_cache()
    {
        require_once "start.php";
        $paths = array(
            'views/default/admin/path_cache_test.php' => 'build/path_cache_info.php' 
                // allows us to test if the path cache actually works like it should
        );
        static::add_paths_in_dir('', 'engine', $paths);
        static::add_paths_in_dir('', 'themes', $paths);        
        static::add_paths_in_dir('', 'engine/controller', $paths);
        static::add_paths_in_dir('', 'engine/cache', $paths);
        static::add_paths_in_dir('', 'engine/mixin', $paths);
        static::add_paths_in_dir('', 'engine/query', $paths);
        static::add_paths_in_dir('', 'engine/feeditem', $paths);
        static::add_paths_in_dir('', 'engine/widget', $paths);
        static::add_paths_in_dir('', 'languages/en', $paths);        
        static::add_paths_in_dir('', 'views/default', $paths);
        static::add_paths_in_dir('', 'views/default/home', $paths);
        static::add_paths_in_dir('', 'views/default/layouts', $paths);        
        static::add_paths_in_dir('', 'views/default/page_elements', $paths);        
        static::add_paths_in_dir('', 'views/default/input', $paths);        
        static::add_paths_in_dir('', 'views/default/messages', $paths);        
        static::add_paths_in_dir('', 'views/default/js', $paths);        
        static::add_paths_in_dir('', 'views/default/translation', $paths);        
        
        /*
        $modules = Config::get('modules');
        foreach ($modules as $module)
        {
            static::add_paths_in_dir("mod/{$module}/", "engine", $paths);
            static::add_paths_in_dir("mod/{$module}/", "languages", $paths);
            static::add_paths_in_dir("mod/{$module}/", "views", $paths);            
        }        
        */
        
        static::write_file("build/path_cache.php", static::get_array_php($paths));
        
        $numPaths = sizeof($paths);
        static::write_file("build/path_cache_info.php", "<div>The path cache is enabled. (size=$numPaths)</div>");
    }
    
    static function write_file($filename, $contents)
    {
        echo strlen($contents) . " ".$filename."\n";
        file_put_contents($filename, $contents);            
    }

    private static function add_paths_in_dir($rel_base, $dir, &$paths, $recursive = false)
    {
        $root = Config::get('root'); 
        $handle = @opendir("{$root}/{$rel_base}{$dir}");
        if ($handle)
        {
            while ($file = readdir($handle))
            {
                $virtual_path = "{$dir}/{$file}";
                $real_rel_path = "{$rel_base}{$virtual_path}";
                $real_path = "{$root}/{$real_rel_path}";

                if (preg_match('/\.php$/', $file))
                {
                    if (!isset($paths[$virtual_path]))
                    {
                        $paths[$virtual_path] = $real_rel_path;
                    }
                }              
                if ($recursive && $file != '.' && $file != '..' && is_dir($real_path))
                {
                    static::add_paths_in_dir($rel_base, $virtual_path, $paths, $recursive);
                }
            }
        }
    }
    
    private static function get_array_php($arr)
    {
        return "<?php return ".var_export($arr, true).";";
    }
    
    static function css($name = '*')
    {    
        require_once "start.php";
        $modules = static::module_glob();
        $css_paths = glob("{views/default/css/$name.php,mod/$modules/views/default/css/$name.php}", GLOB_BRACE);

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
            minify($css_temp, "_media/css/$filename.css", 'css');
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
        require_once "start.php";
        $modules = static::module_glob();
        $js_src_files = glob("{_media/inline_js_src/$name.js,mod/$modules/_media/inline_js_src/$name.js}", GLOB_BRACE);

        foreach ($js_src_files as $js_src_file)
        {
            $basename = pathinfo($js_src_file,  PATHINFO_BASENAME);            
            minify($js_src_file, "_media/inline_js/{$basename}");
        }
    }

    static function all()
    {
        Build::clean();
        Build::lib_cache();
        Build::path_cache();
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