<?php
    echo view('css/default', $vars);
    echo view('css/snippets/site_menu_top', $vars);
    echo view('css/snippets/beige_content', $vars);
    $graphicsDir = "/_graphics/brick";
?>

body { color:#fff; background:#69493e url("<?php echo $graphicsDir; ?>/brick.jpg") repeat left top; }
.heading_container .thin_column, .content_container .thin_column, .footer_container .thin_column { background-color:#2a2a2a; }
#heading h2 { color:#fff; }
#heading h3 { color:#dbc777; }
#heading img { padding-left:10px; }
#site_menu a { color:#fff; }
#site_menu a.selected, #site_menu a:hover { color:#fff; background-color:#be2016; }
#translate_bar { background-color:#764c40;border-color:#7f7f7f; }
.padded {padding:12px 18px; }
.section_header { color:#fff; background:#2a2a2a url("<?php echo $graphicsDir; ?>/section_header.gif") repeat-x left top; }
