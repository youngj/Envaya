<?php
    echo view('js/xhr');
    echo view('js/dom');
    readfile(Config::get('path').'_media/inline_js/reorder_widget.js');