<?php 
    PageContext::add_js_string('ok');
    PageContext::add_js_string('cancel');        
    readfile(Config::get('path').'_media/inline_js/create_modal_box.js');
