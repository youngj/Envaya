<?php
    $graphicsDir = "/_media/images";
?>

html,body
{
    margin:0; padding:0;
}

body
{
    text-align:left;
    font: 80%/1.4  "Lucida Grande", Verdana, sans-serif;
    word-wrap: break-word;
    color: #333333;
}
h1, h2, h3, h4, h5, h6 {
    font-weight: bold;  
    line-height: normal; 
    margin:0; padding: 0;
}

table {
    border-collapse: collapse;
    border-spacing: 0;
}


a {
    color: #4690d6;
    text-decoration: none;
}
a:hover {
    color: #0054a7;
    text-decoration: underline;
}
p {
    margin: 0px 0px 15px 0;
}
img {
    border: none;
}
form {
    margin: 0px;
    padding: 0px;
}

.notitle {
    margin-top:10px;
}
label {
    font-weight: bold;
    color:#333333;
}

#persistent_login label {
    font-size:1.0em;
    font-weight: normal;
}

a.selected
{
    font-weight:bold;
    color:black;
}

.float_right
{    
    display:block;
    float:right;
}

.float_left
{
    display:block;
    float:left;
}

#heading
{
    color:black;
    font-family:Verdana, sans-serif;
}

#heading td
{
    vertical-align:middle;   
}

#heading img
{
    margin:10px 15px 10px 0px;
}

#heading h1, #heading h2
{
    font-size:20px;
    padding-top:5px;
    padding-bottom:0px;
    margin:0px;
}

#heading h1,
#heading h2,
#heading h3
{
    text-align:center;
}

#heading h3
{
    font-size:16px;
    padding:0px;
    margin:0px;
}

#heading .withicon
{
    text-align:left;
}

#heading h2.withicon
{
    padding-top:0px;
}

#heading h3.withicon
{
    padding-top:5px;
}

.messages-exception-detail
{
    font-size:12px;
    font-family:Courier,mono-space;
}

#content
{
    clear:both;
}

.section_header
{
    clear:both;
    padding:10px 15px;
    font-weight:bold;
    font-size:14px;
}

.section_content .section_content
{
    background:transparent none;
}

.message_container
{
    padding:4px;
}

.good_messages, .bad_messages
{
    background:#b9e9ff;
    color:#000000;
    padding:3px;
    border:2px solid #1caeec;
}

.good_messages p, .bad_messages p
{
    margin:4px;
}

.bad_messages
{
    border-color:#CC0000;
    background:#ffcccc;
	font-weight: bold;
	font-size: 14px;
}

.padded
{
    padding:8px 10px;
}

.feed_more
{
    font-size:10px;
    white-space:nowrap;
}

.blog_date
{
    clear:both;
}

.blog_date, .blog_date a
{
    color: #aaa;
    font-size:11px;
}

.blog_date a:hover
{
    text-decoration:underline;
}

.comment_deleted
{
	color: #aaa;
    font-size:11px;
	font-style:italic;
}

.blog_more
{
    float:right;
}
.instructions
{
    padding:5px 0px;
}

.optionLabel
{
    font-weight:normal;
    font-size:100%;
}

.help
{
    color:#666;
    font-style:italic;
}

.input
{
    padding:8px 0px;
}

.websiteUrl
{
    color:green;
    white-space:nowrap;
}

.optionLabelInline
{
    padding-right:10px;
}

.addUpdateButton
{
    float:right;
    margin:4px 0px !important;
}

.view_toggle
{
    float:right;
    padding-top:5px;
    margin-right:10px;
}

.gridTable
{
    width:100%;
}

.gridTable .header_row
{
    background:#444;
    color:#ddd;
}

.gridTable .even td, div.even
{
    background-color:#e8e8e8;
}

.gridTable .odd td, div.odd
{
    background-color:#f3f3f3;
}

.gridTable td
{
    border:1px solid #ccc;
    padding:3px;
}

.gridTable th
{
    padding:3px;
    border:1px solid #666;
    text-align:center;
    vertical-align:middle;
}

.input-checkboxes, .input-radio
{
    border:0px;
}

#language, #viewtype
{
    padding:0px 10px 0px 10px;
}

.footerLinks
{
    padding:5px;
	text-align:center;
}

.partnership_view
{
    padding-bottom:10px;
}

.partnership_view .feed_org_name
{
    font-weight:bold;
}

.contactTable td, .contactTable th
{
    padding:3px;
}

.contactTable th
{
    text-align:right;
}

.gridTable th
{
    text-align:center;
}

.gridTable td
{
    border:1px solid #ccc;
}

.last-paragraph
{
    margin-bottom:0;
}


.feed_snippet
{
    color:#666;
    font-size:12px;
    padding-left:0px;
    padding-bottom:5px;
}

.feed_image_link
{
    float:right;
    margin-left:4px;
    margin-bottom:4px;
    padding:3px;
    background-color:white;
    border: 1px solid #ccc;
}

a.feed_image_link:hover
{
    border: 1px solid #7391a9;
}

.featured_site
{
    clear:both;
    padding-bottom:5px;
}

.featured_site_name
{
    font-weight:bold;
}

.admin_links, .admin_links a
{
    color:red;
    font-size:10px;
}

.capslockWarning
{
    color:red;
    display:none;
    font-weight:normal;
    font-size:11px;
    padding-left:3px;
}

.featured_site_name
{
    padding-bottom:7px;
}

.featured_site img
{
    border:1px solid #ccc;
    margin-top:3px;
    margin-right:15px;
}

#comments
{
    padding:12px;
    margin-top:12px;
    border-top:1px dashed #ccc;
}

.comment
{
    padding-bottom:8px;
}

#comments h4
{
    font-size:16px;
    text-align:center;
}

.separator
{
    border-top:1px solid #f0f0f0;
    height:0px;
    overflow:hidden;
}

.upload_hover
{
    cursor:pointer;
    text-decoration:underline;
}