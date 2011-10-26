<?php
    if ($INCLUDE_COUNT == 0)
    {
        echo view('js/class');
        echo view('js/dom');
        echo "<script type='text/javascript' src='/_media/uploader.js?v".Config::get('hash:js:uploader')."'></script>";
    }