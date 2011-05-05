<?php
    $vars['contentWidth'] = 700;
    
    echo view('css/default', $vars);
    echo view('css/snippets/site_menu_top', $vars);
    echo view('css/snippets/content_margin', $vars);
    echo view('css/snippets/beige_content', $vars);
    $graphicsDir = "/_graphics/cotton";
?>

body { color:#fff; background:#d0b66b url("<?php echo $graphicsDir; ?>/cotton-bg.jpg") repeat left top; }
.heading_container { background:#e3d2a7; }
.content_container .thin_column, .footer_container .thin_column { background-color:#715023; }
#heading h2 , #heading a { color:#715023; }
#heading h3 { color:#a07d28; }
#site_menu a { color:#fff; }
#site_menu a.selected, #site_menu a:hover
{
    color:#000;
    background-color:#e3d2a7;
    -moz-border-radius: 8px;
    -webkit-border-radius: 8px;
}
#translate_bar
{
    color:#000;
    background-color:#c6b186;
    border-color:#e3d2a7;
}
#translate_bar a { color:#000; }
.section_header { color:#fff; background:#bb895a url("<?php echo $graphicsDir; ?>/section_header.gif") repeat-x left top;  }
