<?php
    if ($INCLUDE_COUNT == 0)
    {
        echo view('js/class');
        include_js('inline/dispatcher.js');
    }