<?php
    if ($INCLUDE_COUNT == 0)
    {
        echo "<script type='text/javascript'>".view('js/class').view('js/dom')."</script>";
        echo "<script type='text/javascript' src='/_media/uploader.js?v".Config::get('hash:js:uploader')."'></script>";
    }