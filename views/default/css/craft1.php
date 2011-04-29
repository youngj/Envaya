<?php
    $vars['contentWidth'] = 600;
    
    echo view('css/default', $vars);
    echo view('css/snippets/site_menu_top', $vars);
    echo view('css/snippets/content_margin', $vars);
    echo view('css/snippets/beige_content', $vars);
    $graphicsDir = "/_graphics/craft";
?>

body { color:#fff; background:#f2c346 url("<?php echo $graphicsDir; ?>/craft1-bg.jpg") repeat left -60px; }
.heading_container { background:#121d27; }
.content_container .thin_column, .footer_container .thin_column { background-color:#021019; }
#heading h2 , #heading h2 a { color:#fff; }
#heading h3 { color:#dbc777; }
#site_menu a { color:#fff; }
#site_menu a.selected, #site_menu a:hover
{
    color:#fff;
    background-color:#be2016;
    -moz-border-radius: 8px;
    -webkit-border-radius: 8px;
}
#translate_bar { background-color:#0f2129; border-color:#bb0f07; }
.section_header { color:#fff; background:#0f1f29 url("<?php echo $graphicsDir; ?>/section_header.gif") repeat-x left top;  }
