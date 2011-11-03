<?php

/* 
 * Checks for views, PHP classes, media files, language keys that do not seem to be referenced anywhere
 */

require_once "scripts/cmdline.php";
require_once "scripts/analysis/phpanalyzer.php";
require_once "scripts/analysis/jsanalyzer.php";
require_once "scripts/analysis/cssanalyzer.php";
require_once "scripts/analysis/statemachine/classdef.php";
require_once "scripts/analysis/statemachine/maybeclassref.php";
require_once "scripts/analysis/statemachine/viewdef.php";
require_once "scripts/analysis/statemachine/mediaref.php";
require_once "scripts/analysis/statemachine/maybeviewref.php";
require_once "start.php";

$lang = @$_SERVER['argv'][1] ?: 'en';

class MediaFinder extends Analyzer
{              
    public $paths = array();
  
    function is_checked_file($path)
    {
        return preg_match('#\b(_media)\b#', $path);
    }
    function is_checked_dir($path)
    {
        return preg_match('#\b(_media)\b#', $path) && !preg_match('#\b(www/_media)\b#', $path);
    }    
    
    function parse_file_contents($contents) {}
    
    public function parse_file($path)
    {                   
        $rel_path = $this->get_rel_path($path);
        $this->paths[$rel_path] = $rel_path;
    }
}

$media_finder = new MediaFinder();

$analyzer = new PHPAnalyzer();  
$js_analyzer = new JSAnalyzer();
$css_analyzer = new CSSAnalyzer();
  
$classdef_sm = new StateMachine_ClassDef();    
$classref_sm = new StateMachine_MaybeClassRef();    

$viewdef_sm = new StateMachine_ViewDef();    
$viewref_sm = new StateMachine_MaybeViewRef();    
$mediaref_sm = new StateMachine_MediaRef();

$analyzer->add_state_machine($classdef_sm);    
$analyzer->add_state_machine($classref_sm);
$analyzer->add_state_machine($viewdef_sm);
$analyzer->add_state_machine($viewref_sm);
$analyzer->add_state_machine($mediaref_sm);

$js_analyzer->add_state_machine($mediaref_sm);
$css_analyzer->add_state_machine($mediaref_sm);

$root = dirname(__DIR__);

$analyzer->parse_dir($root);
$media_finder->parse_dir($root);
$js_analyzer->parse_dir($root);
$css_analyzer->parse_dir($root);

foreach ($viewdef_sm->views as $view => $paths)
{
    if (!isset($viewref_sm->views[$view]))
    {
        foreach ($paths as $path)
        {
            echo "view $path\n";
        }
    }
}

foreach ($classdef_sm->class_defs as $class => $paths)
{
    if (!isset($classref_sm->class_refs[$class]))
    {
        foreach ($paths as $path)
        {
            echo "class $path\n";
        }
    }
}

foreach ($media_finder->paths as $path)
{
    if (!isset($mediaref_sm->media_refs[$path]))
    {
        echo "media $path\n";
    }
}