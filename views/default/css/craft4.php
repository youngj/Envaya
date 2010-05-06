<?php
    include(__DIR__."/org.php");
    include(__DIR__."/content_margin.php");
    include(__DIR__."/beige_content.php");
    $graphicsDir = $vars['url'] . "_graphics/craft";
?>

body { color:#fff; background:#f2c346 url("<?php echo $graphicsDir; ?>/craft4-bg.jpg") repeat left -60px; }
.heading_container { background:#461600 url("<?php echo $graphicsDir; ?>/craft4-header.jpg") repeat -80px -20px; }
.content_container .thin_column, .footer_container .thin_column { background-color:#916c4c; }
#heading h2 { color:#fff; }
#heading h3 { color:#dbc777; }
#site_menu a { color:#fff; }
#site_menu a.selected, #site_menu a:hover
{
    color:#fff;
    background-color:#641d09;
    -moz-border-radius: 8px;
    -webkit-border-radius: 8px;
}
#translate_bar { background-color:#641d09; border-color:#e36306; }
.section_header { color:#fff; background:#2a2a2a url("<?php echo $graphicsDir; ?>/section_header.gif") repeat-x left top;  }
