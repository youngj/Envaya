<?php
    $vars['contentWidth'] = Config::get('paragraph_width') + 36;
    echo view('css/default', $vars);
    echo view('css/snippets/site_menu_top', $vars);
?>

#heading img { padding-left:10px; }
.shareLinks { padding-bottom:10px; }
.padded {padding:12px 18px;}
