<?php
    /*
     * Prints a list of PHP files and line numbers that reference code defined in other modules.
     * Useful for finding potential cases where code is defined in the wrong module.
     * 
     * Reference types searched:
     *
     * - 'lang': language keys referenced via "__('foo:bar')"
     * - 'view': view paths referenced via "view('foo/bar')"
     * - 'cref': classes referenced via "new Foo()" or "Foo::bar"
     */

    require_once __DIR__."/../start.php";
    require_once __DIR__."/analysis/phpanalyzer.php";
    require_once __DIR__."/analysis/statemachine/languagekeys.php";
    require_once __DIR__."/analysis/statemachine/views.php";
    require_once __DIR__."/analysis/statemachine/classref.php";

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
                        if ($to_ref_module != $from_module && 
                            !in_array($to_ref_module, $new_ref_names[$from_module]))
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
    
    
    $start_microtime = microtime(true);        

    $analyzer = new PHPAnalyzer();    
    $lang_sm = new StateMachine_LanguageKeys();    
    $view_sm = new StateMachine_Views();    
    $class_sm = new StateMachine_ClassRef();    
    $analyzer->add_state_machine($lang_sm);
    $analyzer->add_state_machine($view_sm);
    $analyzer->add_state_machine($class_sm);
    
    $analyzer->parse_dir(dirname(__DIR__));
    
    $key_modules = get_language_key_modules();
        
    $references = array();

    foreach ($lang_sm->lang_keys as $k => $paths)
    {
        $key_module = @$key_modules[$k] ?: get_path_module(null);
        
        foreach ($paths as $path)
        {
            $ref_module = get_path_module($path);
            
            if ($key_module != $ref_module)
            {
                $ref = array('path' => $path, 'type' => 'lang', 'name' => $k);
                $references[$ref_module][$key_module][] = $ref;
            }
        }
    }
    
    foreach ($view_sm->views as $view => $paths)
    {
        $view_path = get_view_path($view, 'default');
        $view_module = get_path_module($view_path);
        
        foreach ($paths as $path)
        {
            $ref_module = get_path_module($path);
            
            if ($view_module != $ref_module)
            {
                $ref = array('path' => $path, 'type' => 'view', 'name' => $view);
                $references[$ref_module][$view_module][] = $ref;
            }
        }        
    }    
    
    foreach ($class_sm->class_refs as $cls => $paths)
    {
        $file = str_replace('_', '/', strtolower($cls));        
        $cls_path = Engine::get_real_path("engine/$file.php");   

        if ($cls_path == null) // not an envaya class
            continue;
            
        $cls_module = get_path_module($cls_path);
        
        foreach ($paths as $path)
        {
            $ref_module = get_path_module($path);
            
            if ($cls_module != $ref_module)
            {
                $ref = array('path' => $path, 'type' => 'cref', 'name' => $cls);
                $references[$ref_module][$cls_module][] = $ref;
            }
        }        
    }
    
    foreach ($references as $from_module => $to_modules)
    {
        foreach ($to_modules as $to_module => $refs)
        {
            echo "$from_module -> $to_module :\n";
            foreach ($refs as $ref)
            {
                echo "{$ref['type']} {$ref['path']} ({$ref['name']})\n";
            }
            echo "\n";
        }
    }
    
    echo "Summary of direct dependencies:\n";
    foreach ($references as $from_module => $to_modules)
    {
        echo "$from_module -> \n";
        foreach ($to_modules as $to_module => $refs)
        {
            $n = sizeof($refs);
            echo sprintf("%5d", $n)." $to_module\n";
        }        
    }
    echo "\n";    
        
    echo "Summary of transitive dependencies:\n";
    
    $transitive_refs = get_transitive_closure($references);
    
    foreach ($transitive_refs as $from_module => $to_modules)
    {
        echo sprintf('%14s', $from_module)." ->";
        foreach ($to_modules as $to_module)
        {
            echo " $to_module";
        }        
        echo "\n";
    }
    