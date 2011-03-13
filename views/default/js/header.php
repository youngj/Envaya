<?php
    PageContext::add_js_string('page:dirty');    
    readfile(Config::get('path').'_media/inline_js/header.js');