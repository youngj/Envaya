<?php 
    if ($INCLUDE_COUNT == 0)
    {
        PageContext::add_js_string('ok');
        PageContext::add_js_string('cancel');        
        
        echo view('js/dom');
        readfile(Config::get('path').'_media/inline_js/create_modal_box.js');
    }