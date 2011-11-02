<?php
    if ($INCLUDE_COUNT == 0)
    {
        PageContext::add_js_string('password');
        PageContext::add_inline_js_file('inline/password_strength.js');
    }