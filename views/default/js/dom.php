<?php
    if ($INCLUDE_COUNT == 0)
    {
        readfile(Config::get('path').'_media/inline_js/dom.js');
    }