<?php
    if (!isset($vars['contentWidth']))
    {
        $vars['contentWidth'] = 630;
    }
    
    echo view('css/default', $vars);    
    echo view('css/snippets/slideshow', $vars);    
    
    $graphicsDir = "/_graphics";
?>

body
{
    background-color:#545454;
}

#heading h1
{
    color:#e6e6e6;
}

.thin_column
{
    width:<?php echo $vars['contentWidth'] ?>px;
}

.content_container .thin_column
{
    background-color:#191919;
    border:2px solid #a8a8a8;
}

.header_preview
{
    overflow:auto;
    width:<?php echo $vars['contentWidth'] - 40 ?>px;
    margin:5px 0px;
    border:1px solid #ccc;
}

#custom_header_container .imageUploadProgress
{
    overflow:auto;
    width:460px;
}

.header_preview .thin_column
{
    background-color:white;
    border:0px;

}


#content_mid
{
    margin:10px auto 10px auto;
    width:<?php echo $vars['contentWidth'] - 20 ?>px;
    background-color:white;
}

.section_header
{
    border-top:1px solid #c4bfb5;
    border-bottom:1px solid #c4bfb5;
    text-align:center;
    background:#e6e6e6 url("<?php echo $graphicsDir; ?>/green/section_header.gif") repeat-x left -5px;
    height:21px;
    font-family:Verdana, sans-serif;
}

.section_content
{
    background:#fff url("<?php echo $graphicsDir; ?>/green/section_content.gif") repeat-x left -15px;
}

.icon_with_bg
{
    padding:4px;
    vertical-align:middle;
    background:url(<?php echo $graphicsDir; ?>/loggedinarea.png) no-repeat -20px -20px;
}

.dashboard_text_link
{
    display:block;
    padding-top:3px
}

.language
{
    padding-top:5px;
    color:white;
}

.icon_link
{
    color:#333;
    padding-left:39px;
    padding-top:10px;
    background:url(<?php echo $graphicsDir; ?>/editoricons.gif?v4) no-repeat left top;
}

a.icon_link:hover
{
    color:#333;
}

.icon_separator
{
    height:0px;
    border-top:1px solid #fff;
    border-bottom:1px solid #a3a19e;
    margin:0px 15px 0px 35px;
    overflow:hidden;
}

.icon_explore   { background-position:left 7px; }
.icon_help      { background-position:left -33px; }
.icon_home      { background-position:left -73px; }
.icon_logout    { background-position:left -113px; }
.icon_search    { background-position:left -153px; }
.icon_feed      { background-position:left -193px; }
.icon_settings  { background-position:left -233px; }
.icon_design    { background-position:left -273px; }
.icon_photos    { background-position:left -313px; }

#edit_pages_menu a
{
    color:#333;
    display:block;
    float:left;
    line-height: 34px;
    height:34px;
    padding-left:3px;
    margin:0px 1px 2px 0px;
    text-decoration:none;
}

#new_pages_menu
{
    clear:both;
}

#new_pages_menu h4
{
    margin-bottom:4px;
}

#new_pages_menu a
{
    padding-left:6px;
    color:#333;
}

#edit_pages_menu  a:hover
{
    color:black;
    background:#d5d0c8 url(<?php echo $graphicsDir; ?>/green/button.gif) no-repeat left top;
}

#edit_pages_menu a span
{
    padding:0 6px 0 3px;
    height:34px;
    cursor:pointer;
}

#edit_pages_menu a:hover span
{
    background:#d5d0c8 url(<?php echo $graphicsDir; ?>/green/button.gif) no-repeat right top;
    display:block;
}

.log_entry {
    width: 560px;
    font-size: 80%;
    background:white;
    margin:0 10px 5px 10px;
    -webkit-border-radius: 4px;
    -moz-border-radius: 4px;
    border:1px solid white;
}
.log_entry td {
}

.log_entry_user {
    width: 120px;
}

.log_entry_time {
    width: 210px;
    padding:2px;
}

.log_entry_item {

}

.log_entry_action {
    width: 75px;
}


.inputGrid td
{
    padding:0px;
}

.inputGrid .input-textarea, .inputGrid .input-text
{
    margin:0px;
    -webkit-border-radius: 0px;
    -moz-border-radius: 0px;
    border:0px;
}

.attachControls 
{
    width:300px;
}

.attachControls a
{
    display:block;
    padding-left:52px;
    padding-top:6px;
    padding-bottom:6px;
    background:url("/_graphics/attach_controls.gif") no-repeat left top;
}

.attachControls .attachImage
{
    background-position:left top;
    margin-top:2px;
    margin-bottom:4px;
}

.attachControls .attachDocument
{
    background-position:left bottom;
}

.selectMemberButton
{
    float:right;
    padding-top:0px;
}

.selectMemberNotFound label
{
    font-size:100%;
}

.paddedTable td, .paddedTable th
{
    padding:5px;
}

.revisionPreview
{
    margin:5px;
    padding:5px;
    height: 200px;
    border: 1px solid #ccc;
    overflow:auto;
}