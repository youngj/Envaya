<?php
    include(__DIR__."/org.php");
    include(__DIR__."/content_margin.php");

    $graphicsDir = $vars['url'] . "_graphics/sidebar";
?>

.left_sidebar_table, .thin_column
{
    width:750px;
}

.content_container .thin_column
{
    width:770px;
    background:url(<?php echo $graphicsDir ?>/top_plate.gif) no-repeat left top;
    padding-top:20px;
}

#content_wrapper
{
    background:url(<?php echo $graphicsDir ?>/mid_plate.gif) repeat-y left top;
}

#content_bottom
{
    background:url(<?php echo $graphicsDir ?>/bottom_plate.gif) no-repeat left top;
    height:33px;
}

.left_sidebar_table
{
    margin-left:10px;
    
}

#left_sidebar
{
    width:160px;
    border-right:2px solid #e1e1df;
}

#left_sidebar a
{
    display:block;
    padding:5px;
}

.thin_column #content
{
    position:absolute;
    left:175px;
    top:0px;
    width:<?php echo (760-175) ?>px;
    float:left;
}

#heading img
{
    height:50px;
}

#heading h2.withicon
{
    font-size:14px;
    padding-top:5px;
}

#heading h3
{
    font-size:16px;
}