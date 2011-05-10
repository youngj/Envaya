<?php
    if ($vars['include_count'] == 0)
    {
        echo "<script type='text/javascript'>".view('js/class').view('js/dom')."</script>";
        echo "<script type='text/javascript' src='/_media/swfupload.js?v".Config::get('cache_version')."'></script>";
    }