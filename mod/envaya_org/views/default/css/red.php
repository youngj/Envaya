<?php
    $vars['contentWidth'] = 739;
    echo view('css/default', $vars);        
    $graphicsDir = "/_media/images/red";
?>

body
{
    background-color:#e7e2d7;
}

#heading h2 , #heading a { color:black; }

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
    background:#fff url("<?php echo $graphicsDir; ?>/contenttop.png") no-repeat left top;
}

#content_bottom
{
    height:24px;
    background:#fff url("<?php echo $graphicsDir; ?>/contentbottom.png") no-repeat left top;
}

#content_mid
{
    background:#fff url("<?php echo $graphicsDir; ?>/contentgradient.png") repeat-y left top;
    padding:0px 6px;
}

.section_header
{
    background:#e6e6e6 url("<?php echo $graphicsDir; ?>/sectionheader.png") no-repeat left top;
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