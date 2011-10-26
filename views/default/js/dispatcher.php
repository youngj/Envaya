<?php
    if ($INCLUDE_COUNT == 0)
    {
        echo view('js/class');
        PageContext::add_inline_js_file('inline/dispatcher.js');
    }