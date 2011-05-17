<?php
    if ($vars['include_count'] == 0)
    {
        readfile(Config::get('path').'_media/inline_js/swfobject.js');
    }