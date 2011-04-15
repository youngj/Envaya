<?php
    header("Content-type: text/html; charset=UTF-8");   
    echo view('page_elements/header', $vars);
    echo $vars['header'];        
    echo $vars['content'];
    echo view('page_elements/footer', $vars);
