<?php
    include(dirname(__FILE__)."/org.php");
    include(dirname(__FILE__)."/beige_content.php");
    $graphicsDir = $vars['url'] . "_graphics/brick";
?>

body { color:#fff; background:#69493e url("<?php echo $graphicsDir; ?>/brick.jpg") repeat left top; }
.heading_container .thin_column, .content_container .thin_column, .footer_container .thin_column { background-color:#2a2a2a; }
#heading h2 { color:#fff; }
#heading h3 { color:#dbc777; }
#site_menu a { color:#fff; }
#site_menu a.selected, #site_menu a:hover { color:#fff; background-color:#be2016; }
#translate_bar { background-color:#764c40;border-color:#7f7f7f; }
.padded {padding:12px 18px; }
.section_header { color:#fff; background:#2a2a2a url("<?php echo $graphicsDir; ?>/section_header.gif") repeat-x left top; }
