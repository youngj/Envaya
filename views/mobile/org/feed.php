<?php
    $vars['items'] = array_slice($vars['items'], 0, 10);
    echo view('org/feed', $vars, 'default');
?>