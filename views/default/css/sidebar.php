<?php
    include(__DIR__."/org.php");
    include(__DIR__."/content_margin.php");

    $graphicsDir = "/_graphics/sidebar";
?>

.left_sidebar_table, .thin_column
{
    width:750px;
}

.content_container
{
    background:#fff url("<?php echo Config::get('url'); ?>_graphics/simple/bg_gradient.gif") repeat-x left 62px;
}

.content_container .thin_column
{
    width:763px;
    background:url(<?php echo $graphicsDir ?>/top_plate.gif?v2) no-repeat left top;
    padding-top:20px;
}

#content_wrapper
{
    background:url(<?php echo $graphicsDir ?>/mid_plate.gif?v2) repeat-y left top;
}

#content_bottom
{
    background:url(<?php echo $graphicsDir ?>/bottom_plate.gif?v2) no-repeat left top;
    height:33px;
}

.left_sidebar_table
{
    margin-left:6px;
    
}

#left_sidebar
{
    width:160px;
    padding-top:25px;
    border-right:2px solid #e1e1df;
}

#left_sidebar a
{
    display:block;    
    color:#8c8b8b;
    padding-left:20px;
    overflow:hidden;
    padding-top:7px;
    height:28px;  
}

#left_sidebar a:hover
{
    text-decoration:none;
}

#left_sidebar a.selected
{
    color:black;
    background:url(<?php echo $graphicsDir ?>/menu_selected.gif) no-repeat 3px top;
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

#right_content h2
{
    clear:both;
    font-size:18px;
    font-weight:normal;
    padding-top:5px;
    padding-bottom:10px;
    margin-bottom:10px;
    border-bottom:1px dashed #b1b1b1;    
}

#right_content .image_right
{
    border:1px solid #ccc;
    margin-top:3px;
    margin-right:5px;
    margin-left:15px;
    margin-bottom:15px;
}

#right_content h2 small
{
    font-size:15px;
    padding-left:5px;
    color:#808080;
}