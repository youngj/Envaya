<?php 
    if ($INCLUDE_COUNT == 0)
    {
        PageContext::add_js_string('ok');
        PageContext::add_js_string('cancel');        
        
        echo view('js/dom');
        include_js('inline/create_modal_box.js');
    }