<?php
    if (!isset($vars['contentWidth']))
    {
        $vars['contentWidth'] = 900;
    }
    
    echo view('css/editor', $vars);    
?>