<?php
    $contentWidth = 630;
    include(__DIR__."/default.php");
    include(__DIR__."/slideshow.php");    
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
    width:<?php echo $contentWidth ?>px;
}

.content_container .thin_column
{
    background-color:#191919;
    border:2px solid #a8a8a8;
}

.header_preview
{
    overflow:auto;
    width:<?php echo $contentWidth - 40 ?>px;
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
    width:<?php echo $contentWidth - 20 ?>px;
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
    background:url(<?php echo $graphicsDir; ?>/loggedinarea_rounded.gif?v2) no-repeat -20px -20px;
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

#edit_pages_menu a.widget_disabled
{
    color:#999;
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

.report_section_nav
{
    border-bottom: 1px dotted gray;
    text-align:center;
    padding-bottom:5px;
}

.report_section_nav span, .report_section_nav a 
{
    white-space:nowrap;
}

.report_preview_message
{
    padding:7px;
    margin:7px;
    border:1px solid gray;
    background:#f0f0f0;
}

.paddedTable td, .paddedTable th
{
    padding:5px;
}

.report_section_heading
{
    padding-top:8px;
}

#floating_save
{
    position:fixed;
    text-align:center;
    bottom:0px;
    left:0px;
    width:100%;
    border-top:1px solid #666;
    height:42px;
    background-color:#e0e0e0;
}

.floating_save_content
{
    width:600px;
    margin:0 auto;
}

#floating_save .submit_button
{
    margin:0px;    
}

.last_save_time
{
    float:left;
    width:350px;
    height:42px;
    padding-top:12px;
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

.gridDelete
{
    display:block;
    width:30px;
    height:20px;
    background:url("/_graphics/delete.gif?v2") no-repeat left -35px;
}

a.gridDelete:hover
{
    background-position:left -5px;
}
