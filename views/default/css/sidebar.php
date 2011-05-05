<?php
    $vars['contentWidth'] = 600;
    echo view('css/default', $vars);
    echo view('css/snippets/content_margin', $vars);

    $graphicsDir = "/_graphics/sidebar";
?>

#heading h2 , #heading a { color:black; }

.left_sidebar_table, .thin_column
{
    width:750px;
}

.content_container
{
    background:#fff url("/_graphics/simple/bg_gradient.gif") repeat-x left 62px;
}

.content_container .thin_column
{
    width:763px;
    background:url(<?php echo $graphicsDir ?>/top_plate.png) no-repeat left top;
    padding-top:20px;
}

#content_wrapper
{
    background:url(<?php echo $graphicsDir ?>/mid_plate.png) repeat-y left top;
}

#content_bottom
{
    background:url(<?php echo $graphicsDir ?>/bottom_plate.png) no-repeat left top;
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
    background:url(<?php echo $graphicsDir ?>/menu_selected.png) no-repeat 3px top;
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

.section_header
{
    background:#f0f0f0;
    border-top:1px solid #ccc;
    border-bottom:1px solid #ccc;
    height:21px;
    font-family:Verdana, sans-serif;
}