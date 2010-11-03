<?php
    include(__DIR__."/org.php");
    include(__DIR__."/content_margin.php");
    include(__DIR__."/beige_content.php");
    $graphicsDir = "/_graphics/cotton";
?>

body { color:#fff; background:#d0b66b url("<?php echo $graphicsDir; ?>/cotton-bg.jpg") repeat left top; }
.heading_container { background:#e3d2a7; }
.content_container .thin_column, .footer_container .thin_column { background-color:#715023; }
#heading h2 { color:#715023; }
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
    background-image:url("<?php echo $vars['url'] ?>_graphics/world_black.gif");
}
#translate_bar a { color:#000; }
.section_header { color:#fff; background:#bb895a url("<?php echo $graphicsDir; ?>/section_header.gif") repeat-x left top;  }
