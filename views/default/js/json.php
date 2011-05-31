<?php
    if ($INCLUDE_COUNT == 0)
    {
        readfile(Config::get('root').'/_media/json.js');
    }