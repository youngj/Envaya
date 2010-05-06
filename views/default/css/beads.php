<?php
    include(__DIR__."/org.php");
    include(__DIR__."/content_margin.php");
    $graphicsDir = $vars['url'] . "_graphics/beads";
?>

body { color:#fff; background:#f2c346 url("<?php echo $graphicsDir; ?>/beads.jpg") repeat -100px -60px; }
.heading_container { background:#25160d url("<?php echo $graphicsDir; ?>/wood_header.jpg") repeat left bottom; }
.content_container .thin_column,
.footer_container .thin_column { background-color:#090503; }
#heading h2 { color:#fff; }
#heading h3 { color:#cca954; }
#site_menu a { color:#fff; }
#site_menu a.selected, #site_menu a:hover
{
    color:#000;
    background-color:#d1b26c;
}
#translate_bar { background-color:#4e2537; border-color:#a5a180; }
.section_content
{
    background:#fff url("<?php echo $graphicsDir; ?>/section_content.gif") repeat-x left top;
    color:#333;
}
.section_header { color:#fff; background:#4e2237 url("<?php echo $graphicsDir; ?>/section_header.gif") repeat-x left top;  }
