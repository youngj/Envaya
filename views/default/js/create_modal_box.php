<?php 
    if ($INCLUDE_COUNT == 0)
    {
        PageContext::add_js_string('ok');
        PageContext::add_js_string('cancel');        
        
        echo view('js/dom');
        PageContext::add_inline_js_file('inline/create_modal_box.js');
    }