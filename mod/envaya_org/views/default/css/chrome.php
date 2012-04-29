<?php
    $vars['contentWidth'] = Config::get('paragraph_width') + 39;
    echo view('css/default', $vars);        
    $graphicsDir = "/_media/images/chrome";
?>

#site_menu
{
    padding-top:5px;
    text-align:center;
}

#site_menu a
{
    margin:0px 3px;
    white-space:nowrap;
}

#content_top
{
    height:24px;
    background:#fff url("<?php echo $graphicsDir; ?>/contenttop2.png") no-repeat left top;
}

#content_bottom
{
    height:24px;
    background:#fff url("<?php echo $graphicsDir; ?>/contentbottom2.png") no-repeat left top;
}

#content_mid
{
    background:#f3f3f3 url("<?php echo $graphicsDir; ?>/contentgradient2.png") repeat-y left top;       
    padding:0px 6px;
}

.section_header
{
    background:#e6e6e6 url("<?php echo $graphicsDir; ?>/sectionheader2.png") no-repeat left top;
    height:21px;
    font-family:Verdana, sans-serif;
}

.thin_column
{
    width:<?php echo $vars['contentWidth'] - 7 ?>px;
}

#translate_bar
{
    margin-bottom:0px;
}