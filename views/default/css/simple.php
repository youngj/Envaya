<?php
    $contentWidth = 500;
    include(__DIR__."/default.php");

    $graphicsDir = $vars['url'] . "_graphics/simple";
?>

body
{
    background:#fff;
}

.content_container
{
    background:#fff url("<?php echo $graphicsDir; ?>/bg_gradient.gif") repeat-x left 42px;
}

.thin_column
{
    width:<?php echo $contentWidth - 15 ?>px;
}

#site_menu,
#heading h1
{
    background:#fff url("<?php echo $graphicsDir; ?>/headings.gif") repeat-x left bottom;
    height:36px;
    text-align:center;
    font-size:18px;
    font-weight:bold;
    padding-top:9px;
    color:#333;
}

#heading h1.org_only_heading
{
    background-position:left top;
}


#site_menu
{
    font-size:14px;
    padding-top:14px;
    height:31px;
    margin-top:0px;
    font-weight:normal;
}

#site_menu a.selected
{
    color:black;
}

#site_menu a
{
    color:#333;
}

#content_top
{
    height:17px;
    background:#fff url("<?php echo $graphicsDir; ?>/plate.gif?v2") no-repeat -485px -8px;
}

.home #content_top
{
    height:28px;
    background:#fff url("<?php echo $graphicsDir; ?>/plate.gif?v2") no-repeat left 0px;
}


#content_bottom
{
    height:35px;
    margin-top:-10px;
    background:#fff url("<?php echo $graphicsDir; ?>/plate.gif?v2") no-repeat right bottom;
}

#content_mid
{
    background:#fff url("<?php echo $graphicsDir; ?>/plate.gif?v2") repeat-y -970px top;
    padding:0px 2px;
}

#heading
{
    font-size:16px;
    padding:10px 0px 0px 0px;
    margin-top:35px;
}

.orgprofile #heading, .home #heading
{
    margin-top:0px;
}

.home #heading
{
    font-size: 15px;
    color: #666;
    font-family: Verdana, sans-serif;
    padding:20px 10px;
}

.home_heading, .section_header
{
    height:19px;
    width:213px;
    padding:13px 0px;
    text-align:center;
    font:bold 16px Arial;
    background:url("<?php echo $graphicsDir; ?>/home_headings.gif?v2") no-repeat left top;
}

.section_header
{
    margin:0px auto 4px auto;
}

.home_section
{
    background:url("<?php echo $graphicsDir; ?>/home_plate.gif?v4") no-repeat left 31px;
    width:214px;
    margin:0 auto;
    height:190px;
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

.heading_green
{
    background-position:left bottom;
}

.icon_link
{
    background:url(<?php echo $graphicsDir; ?>/homeicons.gif) no-repeat left top;
}

.view_toggle
{
    padding-bottom:3px;
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

.footer_container
{
    font-size:12px;
    color:#333;
}

.footer_container a
{
    color:#555;
}

.tabs
{
    width:100%;
    margin-bottom:5px;
    margin-top:-10px;
    background:#fff url("<?php echo $graphicsDir; ?>/plate.gif?v2") no-repeat -487px -16px;
}

.tab
{
    height:36px;
    text-align:center;
    border-left:1px solid #ddd;
    border-right:1px solid #ddd;
    border-bottom:1px solid #ddd;
}

.tab span
{
    display:block;
    padding:5px 5px;
    color:black;
}

.tabs .active
{
    font-weight:bold;
}

