<?php
    echo view('css/default', $vars);
    echo view('css/snippets/slideshow', $vars);

    $contentWidth = 820;    
    $headerLeftWidth = 340;
    $headerRightWidth = $contentWidth - $headerLeftWidth;
    
    $graphicsDir = "/_graphics/home";
?>

.thin_column
{
    width:882px;
}

#content
{
    background:url(<?php echo $graphicsDir ?>/bg_combined.png) no-repeat left top;
    margin-top:5px;
    padding-top:27px;
    background-color:#fff;
}

#content_mid
{
    background:url(<?php echo $graphicsDir ?>/bg_combined.png) repeat-y right top;
    padding-left:31px;
    padding-right:31px;
}

#content_bottom
{
    background:url(<?php echo $graphicsDir ?>/bg_combined.png) no-repeat -882px bottom;
    height:21px;
}

#home_banner
{
    position:relative;
    height:299px;
    width:<?php echo $contentWidth; ?>px;
}

.home_banner_text
{
    position:absolute;
    top:0px;
    left:0px;
    width:<?php echo $headerLeftWidth; ?>px;
    height:299px;
    background:#333;
}

.home_sticker
{
    position:absolute;
    bottom:0px;
    left:50px;
    width:234px;
    height:150px;    
    background:url(<?php echo $graphicsDir ?>/sticker.jpg) no-repeat left top;
}

.home_sticker_label
{
    padding-top:33px;
    padding-left:15px;
    padding-right:20px;
    text-align:center;
    color:#5b7d26;
    font-weight:bold;
    font-size:16px;
}

.home_button
{
    margin-top:15px;
    margin-left:40px;
    display:block;
    width:142px;
    height:43px;
    background:url(<?php echo $graphicsDir ?>/sprite.png) no-repeat left -71px;    
}

.home_button:hover
{
    text-decoration:none;
    background-position:left -116px;    
}

.home_button span
{
    text-align:center;
    display:block;
    color:white;
    font-weight:bold;
    padding:7px 4px;
    font-size:18px;
}

.home_logo
{
    display:block;
    width:300px;
    height:61px;
    margin:0 auto;
    text-align:center;
    background:url(<?php echo $graphicsDir ?>/sprite.png) no-repeat left top;
}

.home_table
{
    margin-top:4px;
    width:820px;
}

.home_table td
{
    background-color:white;
}

.home_banner_text em
{
    color: #dbea8f;
    font-weight:bold;
    font-family: Verdana, sans-serif;
    font-style:normal;
}

.home_banner_text h1
{
    font-size:19px;
    text-align:center;
    font-weight:normal;
    padding:0px 5px 0px 5px;
    color:#bbb;
    white-space:nowrap;
}

.home_banner_text h1 .centered
{
    display:inline-block;
    text-align:right;
}

.heading_container #heading
{
    display:none;
}   

.content_container
{
    background:#fff url("<?php echo $graphicsDir; ?>/featured_bg.gif") repeat-x left 128px;
}

.home_section_left
{
    background:#fff url(<?php echo $graphicsDir ?>/featured_bg.gif) repeat-x left -10px;
}

.home_content
{
    padding:9px 5px 9px 16px;    
}

.home_about, .home_content
{
    background:transparent url(<?php echo $graphicsDir ?>/circle_shadow.png) repeat-x left -19px;
}
.home_about
{
    background-position:60px -19px;
}

.home_content a
{
    color:#555;
    margin:5px 0px;
}

.icon_link
{
    background:url(<?php echo $graphicsDir; ?>/homeicons.png) no-repeat left top;
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
    height:42px;
    background:url(<?php echo $graphicsDir; ?>/home_headings2.gif) repeat-x left top;
    border: 1px solid black;
}
.home_heading h4
{
    font-weight:bold;
    text-align:center;
    padding-top:10px;
    font-size:14px;
}

.heading_blue
{
    background-position:left top;
    border-color:#bccdd5;
}

.heading_green
{
    background-position:left -42px;
    border-color:#bbc388;
}

.heading_gray
{
    background-position:left -84px;
    border-color:#e3dfd6;
}

.home_section_left
{
    border-right:3px solid #fff;
}

.home_about
{   
    padding:15px 10px 10px 10px;    
    font-size:12px;
}

.home_about .submit_button
{
    margin:0px; padding:0px;
}

.home_bottom_left
{
    background:url(<?php echo $graphicsDir; ?>/anothershadow.png) no-repeat 3px top;
    padding-top:16px;
}

.home_more
{
    font-style:italic;
    font-size:11px;
    white-space:nowrap;
}

.home_featured
{    
    margin-left:1px;
    width:445px;
    padding:2px;
    background:#fff url(<?php echo $graphicsDir; ?>/featured_bg.gif) repeat-x left top;
    border:1px solid #e2dfd6;
    margin-right:3px; 
}

.home_featured_heading
{
    border-bottom:1px solid #c5c4c0;
    padding:10px 15px;
    font-size:14px;
    font-weight:normal;
}

.home_featured_content
{
    border-top:1px solid #fff;
    padding:12px 15px;
}

.home_bottom_right
{
    border-left:1px solid #e3dfd6;
    border-right:1px solid #e3dfd6;
    background:#fff url(<?php echo $graphicsDir; ?>/what_bg.gif) repeat-x left bottom;
}
