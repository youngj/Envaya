<?php
    include(dirname(__FILE__)."/default.php");

    $graphicsDir = $vars['url'] . "_graphics";
?>

body
{
    background-color:white;
}

.thin_column
{
    width:485px;
}

.content_container
{
    background:#fff url("<?php echo $graphicsDir; ?>/green/section_content.gif") repeat-x left 50px;          
}   

#content_top
{
    height:28px;
    background:#fff url("<?php echo $graphicsDir; ?>/plate.gif") no-repeat left top;  
}

#content_bottom
{
    height:35px;
    margin-top:-10px;
    background:#fff url("<?php echo $graphicsDir; ?>/plate.gif") no-repeat right bottom;  
}

#content_mid
{
    background:#fff url("<?php echo $graphicsDir; ?>/plate.gif") repeat-y -485px top;      
    padding:0px 2px;
}

#content_mid .padded
{
    padding-top:0px;
}

#heading
{
    font-size:16px;
    padding:20px 10px;
}

.homeMainHeading
{
    font-size: 15.5px;
    color: #666;
    font-family: arial;
    letter-spacing: 0.5px;
}

.home_heading
{
    height:19px;
    width:203px;
    padding:13px 0px;
    text-align:center;
    font:bold 16px Arial;
    background:url("<?php echo $graphicsDir; ?>/home_headings.gif") no-repeat left top;      
}

.home_section a
{
    color:#555;
    margin:8px 5px 8px 5px;
}

.home_section
{
    background:url("<?php echo $graphicsDir; ?>/home_plate.gif") no-repeat left 30px;      
    width:203px;
    margin:0 auto;
    height:184px;    
}

.heading_green
{
    background-position:left bottom;      
}