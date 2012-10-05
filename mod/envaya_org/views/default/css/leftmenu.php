<?php
    $sidebarWidth = 207;
    $contentWidth = $vars['contentWidth'] = Config::get('paragraph_width') + 22;
    
    $width = $contentWidth + $sidebarWidth + 11; // 920
    
    echo view('css/default', $vars);
    echo view('css/snippets/content_margin', $vars);
    echo view('css/snippets/follow_icon', $vars);    // hack to make follow icons work on /envaya site

    $graphicsDir = "/_media/images/sidebar";
?>

.left_sidebar_table, .thin_column
{
    width:<?php echo $contentWidth + $sidebarWidth; ?>px;
}

.content_container .thin_column
{
    width:<?php echo $width; ?>px;
    padding-top:20px;
}

#content_wrapper
{
    border: 2px solid #e8e8e8;
    border-radius:10px;
    -moz-border-radius:10px;
    margin-bottom:10px;
    padding-bottom:10px;
    padding-left:0px;
    padding-right:0px;    
}

.left_sidebar_table
{
    margin-left:6px;    
}

#left_sidebar
{
    width:<?php echo $sidebarWidth; ?>px;
    padding-top:10px;
    border-right:2px solid #e1e1df;
}

#left_sidebar a
{
    display:block;
    padding-left:18px;
    overflow:hidden;
    padding-top:7px;
    height:28px;  
}

#left_sidebar a:hover
{
    text-decoration:none;
}

#right_content
{
    padding-top:5px;
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
    border-top:1px solid #ccc;
    border-bottom:1px solid #ccc;
    padding:8px 15px;
    font-family:Verdana, sans-serif;
}

#main_content pre
{
    width:<?php echo ($contentWidth - 60); ?>px !important; /* need explicit width in order for IE6 to wrap */
}

#site_menu a.selected
{
    border-width:1px 0px 1px 1px;
    border-style:solid;
    border-color:#ccc;
    border-radius:6px 0px 0px 6px;
}