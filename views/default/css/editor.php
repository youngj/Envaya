<?php
    include(dirname(__FILE__)."/default.php");

    $graphicsDir = $vars['url'] . "_graphics";
?>

body
{
    background-color:#545454;
}

#heading h1
{
    color:#e6e6e6;
}

.thin_column
{
    width:500px;
}

.content_container .thin_column
{
    background-color:#191919;
    border:2px solid #a8a8a8;
}

#content_mid
{
    margin:10px auto 10px auto;
    width:480px;
    background-color:white;
}

.section_header
{
    border-top:1px solid #c4bfb5;
    border-bottom:1px solid #c4bfb5;
    text-align:center;
    background:#e6e6e6 url("<?php echo $graphicsDir; ?>/green/section_header.gif") repeat-x left -5px;  
    height:21px;
    font-family:Verdana, sans-serif;    
}

.section_content
{
    background:#fff url("<?php echo $graphicsDir; ?>/green/section_content.gif") repeat-x left -15px;          
}

.icon_with_bg
{
    padding:4px;
    vertical-align:middle;
    background:url(<?php echo $graphicsDir; ?>/loggedinarea_rounded.gif?v2) no-repeat -20px -20px;
}

.dashboard_text_link
{
    display:block;
    padding-top:3px
}

.language
{
    padding-top:5px;
    color:white;
}

.icon_link
{
    color:#333;
    padding-left:39px;
    padding-top:10px;
    background:url(<?php echo $graphicsDir; ?>/editoricons.gif?v3) no-repeat left top;
}

a.icon_link:hover
{    
    color:#333;
}

.icon_separator
{
    height:0px;
    border-top:1px solid #fff;
    border-bottom:1px solid #a3a19e;
    margin:0px 15px 0px 35px;
    overflow:hidden;
}

.icon_explore   { background-position:left 7px; }
.icon_help      { background-position:left -33px; }
.icon_home      { background-position:left -73px; }
.icon_logout    { background-position:left -113px; }
.icon_search    { background-position:left -153px; }
.icon_feed      { background-position:left -193px; }
.icon_settings  { background-position:left -233px; }
.icon_theme     { background-position:left -273px; }



#edit_pages_menu a
{
    color:#333;    
    display:block;
    float:left;
    line-height: 34px;
    height:34px;
    padding-left:3px;        
    margin:0px 1px 2px 0px;
    text-decoration:none;
}

#edit_pages_menu a.widget_disabled
{
    color:#999;
}

#edit_pages_menu  a:hover
{
    color:black;
    background:#d5d0c8 url(<?php echo $graphicsDir; ?>/green/button.gif) no-repeat left top;
}

#edit_pages_menu a span
{
    padding:0 6px 0 3px;
    height:34px;
    cursor:pointer;
}

#edit_pages_menu a:hover span
{
    background:#d5d0c8 url(<?php echo $graphicsDir; ?>/green/button.gif) no-repeat right top;
    display:block;
}