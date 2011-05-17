<?php    
    if ($vars['include_count'] == 0)
    {
        echo view('js/dom');
        readfile(Config::get('path').'_media/inline_js/post_link.js');
    }