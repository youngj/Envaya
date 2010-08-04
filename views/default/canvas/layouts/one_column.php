<?php 
echo view_layout('one_column_custom_header', 
    "<div class='thin_column'><div id='heading'>{$vars['area1']}</div><div style='clear:both'></div></div>",
    $vars['area2'], @$vars['area3']);    

