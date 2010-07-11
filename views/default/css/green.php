<?php
    include(__DIR__."/org.php");
    include(__DIR__."/content_margin.php");

    $graphicsDir = $vars['url'] . "_graphics/green";
?>

body { background-color:white; }
.heading_container { background-color:#f2f5f6; }
.content_container { background:#fff url("<?php echo $graphicsDir; ?>/section_content.gif") repeat-x left top; }
.content_container .thin_column { background:#e5e5e5 url("<?php echo $graphicsDir; ?>/thin_column.gif") repeat-x left top; }
.section_content { background:#fff url("<?php echo $graphicsDir; ?>/section_content.gif") repeat-x left -15px; }
#site_menu a.selected, #site_menu a:hover { background:#d5d0c8 url(<?php echo $graphicsDir; ?>/button.gif) no-repeat left top; }
#site_menu a.selected span, #site_menu a:hover span { background:#d5d0c8 url(<?php echo $graphicsDir; ?>/button.gif) no-repeat right top; }

.section_header
{
    border-top:1px solid #c4bfb5;
    border-bottom:1px solid #c4bfb5;
    background:#e6e6e6 url("<?php echo $graphicsDir; ?>/section_header.gif") repeat-x left -5px;
    height:21px;
}

.thin_column #content
{
    border-left:1px solid #dbdbdb;
    border-right:1px solid #dbdbdb;
}

