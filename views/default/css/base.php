<?php
    $graphicsDir = $vars['url'] . "_graphics";
?>

html, body, div, span, applet, object, iframe,
h1, h2, h3, h4, h5, h6, p, blockquote, pre,
a, abbr, acronym, address, cite, code,
del, dfn, em, font, img, ins, kbd, q, s, samp,
strike, sub, sup, tt, var,
dl, dt, dd, ol, ul, li,
fieldset, form, label, legend,
table, caption, tbody, tfoot, thead, tr, th, td {
    margin: 0;
    padding: 0;
    border: 0;
    font-weight: inherit;
    font-style: inherit;
    font-size: 100%;
    font-family: inherit;
    vertical-align: baseline;
}

th
{
font-weight:bold;
}

em, i {
    font-style:italic;
}

table {
    border-collapse: collapse;
    border-spacing: 0;
}
caption, th, td
{
    text-align: left;
    vertical-align: top;
}
blockquote:before, blockquote:after,
q:before, q:after {
    content: "";
}
blockquote, q {
    quotes: "" "";
}
.clearfloat {
    clear:both;
    height:0;
    font-size: 1px;
    line-height: 0px;
}

body
{
    text-align:left;
    margin:0 auto;
    padding:0;
    font: 80%/1.4  "Lucida Grande", Verdana, sans-serif;
    color: #333333;
}
a {
    color: #4690d6;
    text-decoration: none;
}
a:visited {

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
ul {
    margin: 0px 0px 15px;
    padding-left: 20px;
}
ul li {
    margin: 0px;
}
ol {
    margin: 0px 0px 15px;
    padding-left: 20px;
}
ul li {
    margin: 0px;
}
form {
    margin: 0px;
    padding: 0px;
}
small {
    font-size: 90%;
}
h1, h2, h3, h4, h5, h6 {
    font-weight: bold;    
}
h1 { font-size: 1.8em; line-height: normal; }
h2 { font-size: 1.5em; line-height: normal; }
h3 { font-size: 1.2em; line-height: normal; }
h4 { font-size: 1.0em; line-height: 1.4; }
h5 { font-size: 0.9em; }
h6 { font-size: 0.8em; }

dt {
    margin: 0;
    padding: 0;
    font-weight: bold;
}
dd {
    margin: 0 0 1em 1em;
    padding: 0;
}
pre, code {
    font-family:Monaco,"Courier New",Courier,monospace;
    font-size:12px;
    background:#EBF5FF;
    overflow:auto;

    overflow-x: auto; /* Use horizontal scroller if needed; for Firefox 2, not needed in Firefox 3 */
    white-space: pre-wrap; /* css-3 */
    white-space: -moz-pre-wrap !important; /* Mozilla, since 1999 */
    white-space: -pre-wrap; /* Opera 4-6 */
    white-space: -o-pre-wrap; /* Opera 7 */
    word-wrap: break-word; /* Internet Explorer 5.5+ */

}
code {
    padding:2px 3px;
}
pre {
    padding:3px 15px;
    margin:0px 0 15px 0;
    line-height:1.3em;
}
blockquote {
    padding:3px 15px;
    margin:0px 0 15px 0;
    line-height:1.3em;
    background:#EBF5FF;
    border:none !important;
    -webkit-border-radius: 5px;
    -moz-border-radius: 5px;
}
blockquote p {
    margin:0 0 5px 0;
}

.notitle {
    margin-top:10px;
}
label {
    font-weight: bold;
    color:#333333;
}

#widget_delete
{
    float:right;
}

#widget_delete div
{
    background-position:left -96px;
}

#widget_delete span
{
    background-position:right -96px;
}

button#widget_delete:hover div
{
    background-position:left -160px;
}

button#widget_delete:hover span
{
    background-position:right -160px;
}

button#widget_delete:active div
{
    background-position:left -128px;
}

button#widget_delete:active span
{
    background-position:right -128px;
}

input[type="submit"] {
    font: 12px/100% Arial, Helvetica, sans-serif;
    font-weight: bold;
    color: #ffffff;
    background:#4690d6;
    border: 1px solid #4690d6;
    -webkit-border-radius: 4px;
    -moz-border-radius: 4px;
    width: auto;
    height: 25px;
    padding: 2px 6px 2px 6px;
    margin:10px 0 10px 0;
    cursor: pointer;
}

#persistent_login label {
    font-size:1.0em;
    font-weight: normal;
}

#topbar
{
    width:100%;
    /* height:48px; */
    background:#1d1d1d url("<?php echo $graphicsDir; ?>/topgradient_sm.gif?v5") repeat-x left -1px;
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

#edit_submenu
{
    text-align:center;
    height:20px;
    padding:3px 10px;
}

#edit_submenu a
{
    color:white;
    font-weight:bold;
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

.adminBox
{
    position:absolute;
    top:75px;
    right:2px;
    border:1px solid red;
    background:#ffcccc;
    padding:5px;
}

.adminBox a
{
    display:block;
    color:#000066;
}

.addUpdateButton
{
    float:right;
    margin:4px 0px !important;
}

#attachControls
{
    padding:4px;
}

#attachControls img
{
    vertical-align:middle;
}

#attachControls a
{
    color:#333;
}

#attachImage input
{
    margin:3px;
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

.header_icons
{
    height:30px;
    width:36px;
    margin:0 auto;
    background:url(<?php echo $graphicsDir ?>/move_edit_delete.gif) no-repeat left top;
}

.down_icon { background-position:left -40px; }
.edit_icon { background-position:left -80px; }
.delete_icon { background-position:left -120px; }
.up_icon { background-position:left -160px; }

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


.commBox
{
    text-align: center;
    height:36px;
    color:white;
    width:100%;
    margin-bottom:3px;
}

.commBox a
{
    font-weight: bold;
    color:white;
}

.commBoxLeft
{
    background:url(<?php echo $graphicsDir ?>/commBox.gif) no-repeat right -10px;
    width:45%;
}

.commBoxMain
{
    background:url(<?php echo $graphicsDir ?>/commBox.gif) repeat-x left -56px;
    white-space:nowrap;
    padding:5px 15px;
}

.commBoxRight
{
    background:url(<?php echo $graphicsDir ?>/commBox.gif) no-repeat left -102px;
    width:45%;
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

.linkUrl, .linkText
{
    padding-bottom:10px;
}

.linkUrl input, .linkText input
{
    width:350px;
    display:block;
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

.top_language
{
    float:right;
    padding:11px 10px 8px 5px;
    color:white;
    color:#e6e6e6;
    white-space:nowrap;
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

.todo_container 
{
    padding-top:5px;
}

.todo_container table
{
    margin:0 auto;
}

.todo_container td
{
    width:250px;
}

.todo_container th
{
    text-align:center;
}

.done_steps li a
{
    color:#999;   
}

.report_view h3
{
    font-size:1.2em;
    padding-top:15px;
    padding-bottom:10px;
    border-bottom: 1px dashed #b2b2b2;
    margin-bottom:10px;
}

.report_view label
{
    font-size:12px;
}

.report_info th
{
    padding-right:10px;    
}

.tabs .active
{
    font-weight:bold;
}
