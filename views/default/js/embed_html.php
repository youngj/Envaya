<?php 
    if ($INCLUDE_COUNT == 0)
    {
        echo view('js/dom');
        include_js('inline/embed_html.js');
    }