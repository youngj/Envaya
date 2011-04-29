<?php
    header("Content-type: text/html; charset=UTF-8");   
    echo view('page_elements/header', $vars);
    if (!@$vars['no_top_bar']) 
    {
        echo view('page_elements/topbar', $vars);
    }
    echo view($vars['layout'], $vars);    
    echo view('page_elements/footer', $vars);