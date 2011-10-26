<?php
    if ($INCLUDE_COUNT == 0)
    {
        echo view('js/dom');
        echo view('js/xhr');    
        
        PageContext::add_js_string('loading');
        PageContext::add_js_string('browse:zoom_in');
        
        PageContext::add_inline_js_file('inline/google_map.js');
    }