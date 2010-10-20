<?php

$vars['width'] = @$vars['width'] ?: 280;

echo view('org/map', $vars, 'default');