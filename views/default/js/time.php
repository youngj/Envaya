<?php 
    if ($INCLUDE_COUNT == 0)
    {
        foreach (Language::get('en')->get_group('date') as $key => $enVal)
        {
            PageContext::add_js_string($key);
        } 
        readfile(Config::get('path').'_media/inline_js/time.js');
    }