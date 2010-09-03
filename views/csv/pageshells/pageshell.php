<?php
    header("Content-type: text/csv; charset=UTF-8");
    $title = preg_replace('/[^\w\-\s]/', '', $vars['title']);
    header('Content-Disposition: attachment; filename="'.$title.'.csv"');
    echo $vars['body'];
