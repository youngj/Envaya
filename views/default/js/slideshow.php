<?php
    if ($INCLUDE_COUNT == 0)
    {
        echo view('js/dom');
        PageContext::add_inline_js_file('inline/slideshow.js');
    }