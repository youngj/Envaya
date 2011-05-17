<?php 
    if ($INCLUDE_COUNT == 0)
    {
        echo view('js/dom');
        readfile(Config::get('path').'_media/inline_js/embed_html.js');
    }