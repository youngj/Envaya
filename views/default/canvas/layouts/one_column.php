<?php 
    echo view("canvas/layouts/one_column_custom_header", array(
        'area1' => "<div class='thin_column'><div id='heading'>{$vars['area1']}</div><div style='clear:both'></div></div>",
        'area2' => $vars['area2'],
        'area3' => @$vars['area3']
    ));
