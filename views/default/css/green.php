<?php
    include(dirname(__FILE__)."/default.php");

    $graphicsDir = $vars['url'] . "_graphics/green";
?>

body
{
    background-color:white;
}

.heading_container
{
    background-color:#f2f5f6;
}

.content_container
{
    background:#fff url("<?php echo $graphicsDir; ?>/section_content.gif") repeat-x left top;          
}   

.content_container .thin_column
{
    background:#e5e5e5 url("<?php echo $graphicsDir; ?>/thin_column.gif") repeat-x left top;
    padding-bottom:1px;
}

.section_header
{
    border-top:1px solid #c4bfb5;
    border-bottom:1px solid #c4bfb5;
    background:#e6e6e6 url("<?php echo $graphicsDir; ?>/section_header.gif") no-repeat left -5px;  
    height:21px;
    font-family:Verdana, sans-serif;
}

.section_content
{
    background:#fff url("<?php echo $graphicsDir; ?>/section_content.gif") repeat-x left -15px;          
}

.thin_column #content
{
    margin:0px auto 10px auto;
    width:478px;
    border-left:1px solid #dbdbdb;
    border-right:1px solid #dbdbdb;
}


#no_site_menu
{
    height:8px;
}


#site_menu
{
    padding-left:8px;
}

#site_menu a
{
    color:#686464;    
    display:block;
    float:left;
    line-height: 34px;
    height:34px;
    padding-left:3px;        
    margin:0px 1px 8px 0px;
    text-decoration:none;
}

#site_menu a.selected,
#site_menu a:hover
{
    color:black;
    background:#d5d0c8 url(<?php echo $graphicsDir; ?>/button.gif) no-repeat left top;
}

#site_menu a span
{
    padding:0 6px 0 3px;
    height:34px;
    cursor:pointer;
}

#site_menu a.selected span,
#site_menu a:hover span
{
    background:#d5d0c8 url(<?php echo $graphicsDir; ?>/button.gif) no-repeat right top;
    display:block;
}

.language
{
    padding-top:5px;
}