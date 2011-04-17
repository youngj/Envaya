<?php

$vars['width'] = @$vars['width'] ?: 280;

echo view('output/map', $vars, 'default');