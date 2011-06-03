<?php

/*
 * Minifies javascript and CSS files, copying static files to www/_media/
 * and saving cache information in build/.
 *
 * Usage:
 *  php make.php [clean|js|css|media|lib_cache|path_cache|all]
 */     

chdir(__DIR__);
 
require_once "scripts/cmdline.php";

class Build
{
    private static function module_glob()
    {
        return "{".implode(',',Config::get('modules'))."}";
    }    
    
    private static function minify($srcFile, $destFile, $type='js')
    {
        $src = file_get_contents($srcFile);
        
        system("java -jar vendors/yuicompressor-2.4.2.jar --type $type -o ".escapeshellarg($destFile). " ".escapeshellarg($srcFile));
        
        $compressed = file_get_contents($destFile);

        echo strlen($src)." ".strlen($compressed)." $destFile\n";  
    }
    
    static function clean()
    {
        @unlink("build/lib_cache.php");
        @unlink("build/path_cache.php");
        
        system('rm -rf www/_media/*');        
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
        static::add_paths_in_dir('', 'views/default/layouts', $paths);        
        static::add_paths_in_dir('', 'views/default/page_elements', $paths);        
        static::add_paths_in_dir('', 'views/default/input', $paths);        
        static::add_paths_in_dir('', 'views/default/messages', $paths);        
        static::add_paths_in_dir('', 'views/default/js', $paths);        
        static::add_paths_in_dir('', 'views/default/translation', $paths);        
                
        $modules = Config::get('modules');
        foreach ($modules as $module)
        {
            static::add_paths_in_dir("mod/{$module}/", 'views/default/page_elements', $paths);
            static::add_paths_in_dir("mod/{$module}/", 'views/default/home', $paths);
            static::add_paths_in_dir("mod/{$module}/", 'engine/controller', $paths);
            static::add_paths_in_dir("mod/{$module}/", "languages/en", $paths);            
            static::add_paths_in_dir("mod/{$module}/", "themes", $paths);            
        }        
        
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
    
    /* 
     * Minify all CSS files defined in each module's views/default/css/ directory, and copy to www/_media/css/.
     */       
    static function css($name = '*')
    {    
        require_once "start.php";
        
        $modules = static::module_glob();
        $css_paths = glob("{views/default/css/$name.php,mod/$modules/views/default/css/$name.php}", GLOB_BRACE);

        $output_dir = 'www/_media/css';
        
        if (!is_dir($output_dir))
        {
            mkdir($output_dir);
        }
        
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
            static::minify($css_temp, "$output_dir/$filename.css", 'css');
            unlink($css_temp);
        }
    }
     
    private static function js_minify_dir($base, $name = '*', $dir = '')
    {    
        $js_src_files = glob("$base/js/{$dir}{$name}.js");
        foreach ($js_src_files as $js_src_file)
        {
            $basename = pathinfo($js_src_file,  PATHINFO_BASENAME);
            static::minify($js_src_file, "www/_media/{$dir}{$basename}");
        }
        
        $subdirs = glob("$base/js/{$dir}*", GLOB_ONLYDIR);
        foreach ($subdirs as $subdir)
        {
            $basename = pathinfo($subdir,  PATHINFO_BASENAME);
        
            if (!is_dir("www/_media/{$dir}{$basename}"))
            {
                mkdir("www/_media/{$dir}{$basename}");
            }
            static::js_minify_dir($base, $name, "{$dir}{$basename}/");
        }
    }

    /* 
     * Minify Javascript in each module's js/ directory, and copy to www/_media/.
     */   
    static function js($name = '*')
    {    
        require_once "start.php";
        $modules = static::module_glob();
        
        static::js_minify_dir(".", $name);
        
        foreach (Config::get('modules') as $module)
        {            
            static::js_minify_dir("mod/$module", $name);
        }
    }
    
    static function system($cmd)
    {
        echo "$cmd\n";
        return system($cmd);
    }
    
    /* 
     * Copy static files from each module's _media/ directory to www/_media/.
     */
    static function media()
    {
        require_once "start.php";
        
        static::system("rsync -rp _media/ www/_media/");
        
        foreach (Config::get('modules') as $module)
        {            
            if (is_dir("mod/$module/_media"))
            {
                static::system("rsync -rp mod/$module/_media/ www/_media/");
            }
        }
    }

    static function all()
    {
        Build::clean();
        Build::lib_cache();
        Build::path_cache();
        Build::css();
        Build::js();
        Build::media();
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