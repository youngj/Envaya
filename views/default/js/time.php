<?php 
    if ($INCLUDE_COUNT == 0)
    {
        foreach (Language::get('en')->get_group('date') as $key => $enVal)
        {
            PageContext::add_js_string($key);
        } 
        PageContext::add_inline_js_file('inline/time.js');
    }