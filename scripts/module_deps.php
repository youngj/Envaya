<?php
    /*
     * Prints a list of PHP files and line numbers that reference code defined in other modules.
     * Useful for finding potential cases where code is defined in the wrong module.
     * 
     * Reference types searched:
     *
     * - lang:   language keys referenced via "__('foo:bar')"
     * - view:   view paths referenced via "view('foo/bar')"
     * - engine: envaya Engine classes referenced via "new Foo()" or "Foo::bar"
     * - media:  static files referenced via a "_media" URL
     * - url:    web pages on Envaya referenced via a relative URL starting with "/" (but not "/_media")
     * - css:    css classes or ids referenced via an 'id' or 'class' attribute on an HTML element    
     */

    require_once __DIR__."/../start.php";
    require_once __DIR__."/analysis/phpanalyzer.php";
    require_once __DIR__."/analysis/jsanalyzer.php";
    require_once __DIR__."/analysis/statemachine/languagekeys.php";
    require_once __DIR__."/analysis/statemachine/views.php";
    require_once __DIR__."/analysis/statemachine/classref.php";
    require_once __DIR__."/analysis/statemachine/mediaref.php";
    require_once __DIR__."/analysis/statemachine/urlref.php";
    require_once __DIR__."/analysis/statemachine/cssref.php";
    require_once __DIR__."/analysis/statemachine/cssdef.php";

    function get_path_module($path)
    {
        if (preg_match('#mod/(\w+)/#', $path, $matches))
        {
            return $matches[1];
        }
        else if ($path)
        {
            return '(core)';
        }
        else
        {
            return '(unknown)';
        }
    }
    
    function print_module_references($references)
    {
        foreach ($references as $from_module => $to_modules)
        {
            foreach ($to_modules as $to_module => $refs)
            {
                if ($from_module == $to_module)
                    continue;
            
                echo "$from_module -> $to_module :\n";
                foreach ($refs as $ref)
                {
                    echo sprintf('%-6s', $ref['type'])." {$ref['path']} ({$ref['name']})\n";
                }
                echo "\n";
            }
        }
        
        
        echo "Summary of reference types:\n";
        
        $type_count = array();
        foreach ($references as $from_module => $to_modules)
        {
            foreach ($to_modules as $to_module => $refs)
            {
                foreach ($refs as $ref)
                {
                    $type = $ref['type'];
                    if (isset($type_count[$type]))
                    {
                        $type_count[$type]++;
                    }
                    else
                    {
                        $type_count[$type] = 1;
                    }
                }
            }
        }
        
        asort($type_count);
        foreach ($type_count as $type => $count)
        {
            echo sprintf("%-6s", $type)." $count\n";
        }
        echo "\n";
        
        echo "Summary of direct references:\n";
        foreach ($references as $from_module => $to_modules)
        {
            echo "$from_module -> \n";
            
            $module_ref_count = array();            
            foreach ($to_modules as $to_module => $refs)
            {
                $module_ref_count[$to_module] = sizeof($refs);
            }
            
            asort($module_ref_count);
            $module_ref_count = array_reverse($module_ref_count, true);
            
            foreach ($module_ref_count as $to_module => $num_refs)
            {
                $name = ($to_module == $from_module) ? '(self)' : $to_module;
            
                echo sprintf("%5d", $num_refs)." $name\n";
            }        
            
        }
        echo "\n";    
            
        echo "Summary of transitive dependencies:\n";
        
        $transitive_refs = get_transitive_closure($references);
        
        foreach ($transitive_refs as $from_module => $to_modules)
        {
            echo sprintf('%14s', $from_module)." ->";
            
            if (!$to_modules || $to_modules == array($from_module))
            {
                echo ' (none)';
            }
            else
            {
                foreach ($to_modules as $to_module)
                {
                    if ($from_module == $to_module)
                        continue;            
                
                    echo " $to_module";
                }     
            }
            echo "\n";
        }                
    }
    
    function get_transitive_closure($references)
    {
        $ref_names = array();
        
        $module_names = array_keys($references);
    
        foreach ($module_names as $from_module)
        {    
            $ref_names[$from_module] = array_keys($references[$from_module]);
        }
    
        while (true)
        {
            $new_ref_names = $ref_names;
            $has_new_refs = false;
            
            foreach ($module_names as $from_module)
            {
                $to_modules = $ref_names[$from_module];
                foreach ($to_modules as $to_module)
                {
                    $to_ref_modules = $ref_names[$to_module];
                    if (!$to_ref_modules)
                        continue;
                    
                    foreach ($to_ref_modules as $to_ref_module)
                    {
                        if (!in_array($to_ref_module, $new_ref_names[$from_module]))
                        {
                            $new_ref_names[$from_module][] = $to_ref_module;
                            $has_new_refs = true;
                        }
                    }
                }
            }
            
            if (!$has_new_refs)
                return $ref_names;
            
            $ref_names = $new_ref_names;   
        }    
    }
    
    function get_language_key_modules()
    {
        $en = Language::get('en');
        foreach ($en->get_all_group_names() as $group_name)
        {
            $path = $en->get_group_path($group_name);
            $group = include($path);
            
            $module_name = get_path_module($path);
            
            foreach ($group as $key => $en_trans)
            {
                $key_modules[$key] = $module_name;
            }
        }
        return $key_modules;
    }

    function get_engine_class_path($cls)
    {
        $file = str_replace('_', '/', strtolower($cls));        
        return Engine::get_real_path("engine/$file.php");
    }

    function get_view_path($view)
    {
        return Views::get_path($view, 'default')
            ?: Views::get_path($view, 'mobile')
            ?: Views::get_path($view, 'rss');
    }
    
    function get_module_references($dir)
    {
        $lang_sm = new StateMachine_LanguageKeys();    
        $view_sm = new StateMachine_Views();    
        $class_sm = new StateMachine_ClassRef();    
        $media_sm = new StateMachine_MediaRef();    
        $url_sm = new StateMachine_URLRef();    
        $css_sm = new StateMachine_CSSRef();    
        $cssdef_sm = new StateMachine_CSSDef();    
        
        $analyzer = new PHPAnalyzer();    
        $analyzer->add_state_machine($lang_sm);
        $analyzer->add_state_machine($view_sm);
        $analyzer->add_state_machine($class_sm);
        $analyzer->add_state_machine($media_sm);
        $analyzer->add_state_machine($url_sm);
        $analyzer->add_state_machine($css_sm);
        $analyzer->add_state_machine($cssdef_sm);
        
        $js_analyzer = new JSAnalyzer();
        $js_analyzer->add_state_machine($media_sm);
        $js_analyzer->add_state_machine($url_sm);
        
        $js_analyzer->parse_dir($dir);
        $analyzer->parse_dir($dir);
        
        $key_modules = get_language_key_modules();
            
        $references = array();

        foreach ($lang_sm->lang_keys as $k => $paths)
        {
            $key_module = $key_modules[$k] ?: get_path_module(null);

            foreach ($paths as $path)
            {
                $ref_module = get_path_module($path);
                
                $ref = array('path' => $path, 'type' => 'lang', 'name' => $k);
                $references[$ref_module][$key_module][] = $ref;
            }
        }
        
        foreach ($view_sm->views as $view => $paths)
        {
            $view_path = get_view_path($view);
            
            $view_module = get_path_module($view_path);
            
            foreach ($paths as $path)
            {
                $ref_module = get_path_module($path);
                
                $ref = array('path' => $path, 'type' => 'view', 'name' => $view);
                $references[$ref_module][$view_module][] = $ref;
            }        
        }    
        
        foreach ($class_sm->class_refs as $cls => $paths)
        {
            $cls_path = get_engine_class_path($cls);

            if ($cls_path == null) // not an envaya class
                continue;
                
            $cls_module = get_path_module($cls_path);
            
            foreach ($paths as $path)
            {
                $ref_module = get_path_module($path);
                
                $ref = array('path' => $path, 'type' => 'engine', 'name' => $cls);
                $references[$ref_module][$cls_module][] = $ref;
            }        
        }
        

        foreach ($media_sm->media_refs as $media_url => $paths)
        {
            $media_path = Engine::get_real_path($media_url)
                ?: Engine::get_real_path(str_replace('_media', 'js', $media_url))
                ?: Engine::get_real_path(
                    str_replace('.css', '.php', 
                        str_replace('_media/css', 'views/default/css', $media_url)
                    )
                );
            $media_module = get_path_module($media_path);
            
            foreach ($paths as $path)
            {
                $ref_module = get_path_module($path);
                
                $ref = array('path' => $path, 'type' => 'media', 'name' => $media_url);
                $references[$ref_module][$media_module][] = $ref;
            }        
        }
        
        $css_defs = $cssdef_sm->css_defs;
        
        foreach ($css_sm->css_refs as $css_ref => $paths)
        {        
            if (!isset($css_defs[$css_ref])) // css class or id used without definition
            {
                continue;
            }
            $css_def_path = $css_defs[$css_ref];

            $css_module = get_path_module($css_def_path);            
            
            foreach ($paths as $path)
            {
                $ref_module = get_path_module($path);                
                $ref = array('path' => $path, 'type' => 'css', 'name' => $css_ref);
                $references[$ref_module][$css_module][] = $ref;
            }
        }

        $request = new Request(null);
        $controller = new Controller_Default($request);

        foreach ($url_sm->url_refs as $url => $paths)
        {
            $controller_action = $controller->get_controller_action($url);            
            $cls = get_class($controller_action[0]);
            
            // URLs that get mapped to these controllers are probably not actually URLs
            if ($cls == 'Controller_Default' || $cls == 'Controller_UserSite')
            {
                continue;
            }
                        
            $path = get_engine_class_path($cls);
            $url_module = get_path_module($path);
            
            foreach ($paths as $path)
            {
                $ref_module = get_path_module($path);
                
                $ref = array('path' => $path, 'type' => 'url', 'name' => $url);
                $references[$ref_module][$url_module][] = $ref;
            }        
        }        
        
        return $references;
    }
    
    $references = get_module_references(dirname(__DIR__));
    print_module_references($references);
    