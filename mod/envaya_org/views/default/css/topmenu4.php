<?php
    $vars['contentWidth'] = Config::get('paragraph_width') + 26;
    echo view('css/default', $vars);
    echo view('css/snippets/site_menu_top', $vars);
    //echo view('css/snippets/content_margin', $vars);    
?>

#site_menu
{
    padding-left:0px;
    padding-bottom:5px;
}

.section_header
{
    border-top:1px solid #ccc;
    border-bottom:1px solid #ccc;
}

#main_content
{
    border: 3px solid #fff;
    border-radius:4px;
    margin-bottom:4px;
    -moz-border-radius:4px;
}

.thin_column #content
{
    margin-bottom:10px;
}

#site_menu a.selected, #site_menu a:hover
{
    border-radius: 8px;
}
