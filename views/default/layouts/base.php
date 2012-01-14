<?php
    PageContext::set_http_header('Content-Type', "text/html; charset=UTF-8");       
            
    $body_start = view('page_elements/body_start', $vars);
    $layout = view($vars['layout'], $vars);    
    $end = view('page_elements/html_end', $vars);
    
    // render html <head> tag last to allow layout to modify HTML head via PageContext::add_header_html(), et al.
    
    echo view('page_elements/html_start', $vars);
    echo $body_start;
    echo $layout;
    echo $end;