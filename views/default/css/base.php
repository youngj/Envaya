<?php
    $graphicsDir = "/_graphics";
?>

html,body
{
    margin:0; padding:0;
}

body
{
    text-align:left;
    font: 80%/1.4  "Lucida Grande", Verdana, sans-serif;
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

#site_menu
{
    clear:both;
    padding-top:8px;
    text-align:center;
}

#edit_pages_menu
{
   text-align:center;
}

#site_menu a, #edit_pages_menu a
{
    margin:0px 3px;
    white-space:nowrap;
}

a.selected
{
    font-weight:bold;
    color:black;
}

.float_right
{
    clear:both;
    display:block;
    float:right;
}

#heading
{
    padding:10px;
    color:black;
    font-family:Verdana, sans-serif;
}

#heading img
{
    float:left;
    margin-right:15px;
    margin-bottom:10px;
}

#heading h1, #heading h2
{
    color:#222;
    font-size:22px;
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


#heading h2.withicon
{
    padding-top:20px;
    text-align:left;
}

#heading h3
{
    color:#222;
    font-size:14px;
    padding:0px;
    margin:0px;
}

#heading h3.withicon
{
    text-align:left;
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

.section_content
{
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
}

.padded
{
    padding:8px 10px;
}

.blog_post
{
    clear:both;
}

.blog_post_wrapper
{
    border-bottom:1px solid #ddd;
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

.gridTable .even td
{
    background-color:#e8e8e8;
}

.gridTable .odd td
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

#viewtype
{
    float:right;
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

.icon_link
{
    display:block;
    height:30px;
    padding-top:3px;
    padding-left:36px;
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

.image_left
{
    float:left;
    clear:left;
    margin-right:8px;
    margin-bottom:8px;
}
.image_right
{
    float:right;
    clear:right;
    margin-left:8px;
    margin-bottom:8px;
}
.image_center
{
    display:block;
    margin:0 auto;
    text-align:center;
}

.feed_snippet
{
    color:#666;
    font-size:11px;
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
