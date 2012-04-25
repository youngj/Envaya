<?php
    if ($INCLUDE_COUNT == 0)
    {
        echo view('js/class');
        echo view('js/dom');
        echo "<script type='text/javascript' src='/_media/uploader.".Config::get('build:hash:js:uploader').".js'></script>";
    }