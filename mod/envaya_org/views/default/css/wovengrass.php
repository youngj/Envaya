<?php
    $vars['contentWidth'] = 680;
    
    echo view('css/default', $vars);
    echo view('css/snippets/site_menu_top', $vars);
    echo view('css/snippets/content_margin', $vars);
    
    $graphicsDir = "/_media/images/wovengrass";
?>

body { color:#000; background:#d5b24a url("<?php echo $graphicsDir; ?>/woven-grass.jpg") repeat left -60px; }
.heading_container { background:#fff; }
.content_container .thin_column,
.footer_container .thin_column { background:#f0e3a7 url("<?php echo $graphicsDir; ?>/woven-grass-2-textbg.jpg") repeat -30px -60px; }
#heading h2 , #heading a { color:#333; }
#heading h3 { color:#a07d28; }
#site_menu a { color:#000; }
#site_menu a.selected, #site_menu a:hover
{
    color:#fff;
    background-color:#a07d28;
    -moz-border-radius: 8px;
    -webkit-border-radius: 8px;
}
#translate_bar { background-color:#a68c4d; border-color:#f4ebc5; }
.section_header { color:#fff; background:#ad9e61 url("<?php echo $graphicsDir; ?>/section_header.gif") repeat-x left top;  }
.section_content
{
    background:#fdffe9 url("<?php echo $graphicsDir; ?>/section_content.gif") repeat-x left top;
    color:#333;
}
