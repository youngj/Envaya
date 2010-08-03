<?php
    include(__DIR__."/default.php");
    $graphicsDir = $vars['url'] . "_graphics/home";
?>


.home .thin_column
{
    width:760px;
}

.home_banner
{
    height:322px;
    background:url(<?php echo $graphicsDir ?>/banner_phone.jpg) no-repeat left top;
}

.home_content_bg
{
    padding:0px 5px 5px 5px;
    background-color:#666;
}

.home_table
{
    margin-top:3px;
    width:750px;
}

.home_table td
{
    background-color:white;
}

.home_banner h1
{
    font-size: 17px;
    color: #fff;
    font-weight:bold;
    font-family: Verdana, sans-serif;
    padding:20px 20px;
    width:400px;
}

.heading_container #heading
{
    display:none;
}   

.content_container
{
    background:#fff url("<?php echo $vars['url']; ?>/_graphics/simple/bg_gradient.gif") repeat-x left 135px;
}

.home_content
{
    padding:9px 12px;
}

.home_content a
{
    color:#555;
    margin:5px 0px;
}

.icon_link
{
    background:url(<?php echo $graphicsDir; ?>/homeicons.gif) no-repeat left top;
}

.icon_signup            { background-position:left -80px; }
a.icon_signup:hover     { background-position:left -120px; }

.icon_help              { background-position:left 0px; }
a.icon_help:hover       { background-position:left -40px; }

.icon_logout            { background-position:left -160px; }
a.icon_logout:hover     { background-position:left -200px; }

.icon_explore           { background-position:left -240px; }
a.icon_explore:hover    { background-position:left -280px; }

.icon_search            { background-position:left -320px; }
a.icon_search:hover     { background-position:left -360px; }

.icon_feed              { background-position:left -400px; }
a.icon_feed:hover       { background-position:left -440px; }

.home_heading
{
    height:40px;
    background:url(<?php echo $graphicsDir; ?>/home_headings.gif) repeat-x left top;
    border-top: 1px solid #e8e6de;
}
.home_heading div
{
    font-weight:bold;
    text-align:center;
    padding-top:9px;
    font-size:14px;
}

.heading_blue
{
    background-position:left -9px;
}

.heading_green
{
    background-position:left -58px;
}

.heading_gray
{
    background-position:left -107px;
}

.home_content
{
    background-color:#f8f6f3;
}

.home_section
{
    border-left:1px solid #fff;
    border-right:1px solid #ddd;
    border-top:1px solid #e8e6de;
}

.home_about
{   
    padding:15px 10px;    
    font-size:12px;
}

.home_featured
{    
    padding:10px;
    border:1px solid #ccd0d0;
    background-color:#f8f6f3;
}

.home_featured_heading
{
    font-size:10px;
    color:#666;
}
.home_featured_name
{
    font-weight:bold;
}
