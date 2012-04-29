<?php
    $contentWidth = $vars['contentWidth'] = 700;
    
    echo view('css/default', $vars);

    $graphicsDir = "/_media/images/simple";
?>

body
{
    background:#fafafa;
}

.content_container
{
}

.thin_column
{
    width:<?php echo $contentWidth; ?>px;
}

#site_menu,
#heading h1
{
    font-size:27px;
    font-weight:bold;
    padding-bottom:25px;
    padding-left:10px;
    color:#363;
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

#content
{
    background:#fdfdfd;
    border: 2px solid #e8e8e8;
    border-radius:10px;
    -moz-border-radius:10px;
    margin-bottom:10px;
    padding-top:8px;
    padding-bottom:5px;
    padding-left:5px;
    padding-right:5px;
    box-shadow: 1px 1px 10px #ccc;
    -moz-box-shadow: 1px 1px 10px #ccc;
}

#heading
{
    font-size:16px;
    padding:10px 0px 0px 0px;
    margin-top:25px;
}

.section_header
{
    height:19px;
    width:213px;
    padding:13px 0px;
    text-align:center;
    font:bold 16px Arial;
}

.section_header
{
    margin:0px auto 4px auto;
}

.heading_green
{
    background-position:left bottom;
}


.view_toggle
{
    padding-bottom:3px;
}

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
    margin-bottom:10px;    
}

.tab
{
    height:36px;
    text-align:center;
    border-bottom:1px solid #ddd;
}

.tab span
{
    display:block;
    padding:5px 5px;
    color:black;
}

