<?php
    if ($INCLUDE_COUNT == 0)
    {
        PageContext::add_inline_js(file_get_contents(Config::get('root').'/_media/json.js'));
    }