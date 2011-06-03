<?php
    if ($INCLUDE_COUNT == 0)
    {
        echo view('js/dom');
        echo view('js/xhr');    
        
        PageContext::add_js_string('loading');
        PageContext::add_js_string('map:zoom_in');
        
        include_js('inline/google_map.js');
    }