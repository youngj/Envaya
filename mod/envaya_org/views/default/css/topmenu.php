<?php
    $vars['contentWidth'] = Config::get('paragraph_width') + 42;
    echo view('css/default', $vars);
    echo view('css/snippets/site_menu_top', $vars);
    echo view('css/snippets/content_margin', $vars);    
?>