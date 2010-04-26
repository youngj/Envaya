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
    background: #dedede;
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
    line-height: normal;
}
h1 { font-size: 1.8em; }
h2 { font-size: 1.5em; }
h3 { font-size: 1.2em; }
h4 { font-size: 1.0em; }
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

/* canvas layout: 1 column, no sidebar */
#one_column {
/*  width:928px; */
    margin:0;
    min-height: 360px;
    background: #dedede;
    padding:0 0 10px 0;
    -webkit-border-radius: 8px; 
    -moz-border-radius: 8px;
}

.contentWrapper {
    background:white;
    -webkit-border-radius: 8px; 
    -moz-border-radius: 8px;
    padding:10px;
    margin:0 10px 10px 10px;
}
span.contentIntro p {
    margin:0 0 0 0;
}
.notitle {
    margin-top:10px;
}
label {
    font-weight: bold;
    color:#333333;
    font-size: 120%;
}
input {
    font: 120% Arial, Helvetica, sans-serif;
    padding: 5px;
    border: 1px solid #cccccc;
    color:#666666;
    -webkit-border-radius: 5px; 
    -moz-border-radius: 5px;
}
textarea {
    font: 120% Arial, Helvetica, sans-serif;
    border: solid 1px #cccccc;
    padding: 5px;
    color:#666666;
    -webkit-border-radius: 5px; 
    -moz-border-radius: 5px;
}
textarea:focus, input[type="text"]:focus {
    border: solid 1px #4690d6;
    background: #e4ecf5;
    color:#333333;
}

.submit_button 
{
    font: 12px/100% Arial, Helvetica, sans-serif;
    font-weight: bold;
    color: #ffffff;
    background:transparent; 
    border:0px;
    padding:5px;
    width: auto;
    margin:10px 0px;
    cursor: pointer;
}

.submit_button div
{
    background: url(<?php echo $graphicsDir ?>/buttons.gif?v2) left -32px; 
    display:block;
    height:32px;
    margin:0px;
    padding-left:9px;
}

.submit_button span
{
    background: url(<?php echo $graphicsDir ?>/buttons.gif?v2) right -32px; 
    display:block;
    height:24px;
    padding-top:8px;
    padding-right:9px;
    white-space:nowrap;
}

button.submit_button:hover div
{
    background-position:left top;
}

button.submit_button:hover span
{
    background-position:right top;
}

button.submit_button:active div
{
    background-position:left -64px;
}

button.submit_button:active span
{
    background-position:right -64px;
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


/* 
.submit_button:hover, input[type="submit"]:hover {
    background: #0054a7;
    border-color: #0054a7;
}

*/ 

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
.cancel_button {
    font: 12px/100% Arial, Helvetica, sans-serif;
    font-weight: bold;
    color: #999999;
    background:#dddddd;
    border: 1px solid #999999;
    -webkit-border-radius: 4px; 
    -moz-border-radius: 4px;
    width: auto;
    height: 25px;
    padding: 2px 6px 2px 6px;
    margin:10px 0 10px 10px;
    cursor: pointer;
}
.cancel_button:hover {
    background: #cccccc;
}

.input-text,
.input-tags,
.input-url,
.input-textarea {
    width:448px;
    margin-top:4px;
}

.input-textarea {
    height: 200px;
    margin-top:4px;
}

.input-file {
    margin-top:4px;
} 

#persistent_login label {
    font-size:1.0em;
    font-weight: normal;
}

/* ***************************************
    PAGINATION
*************************************** */
.pagination {
    -webkit-border-radius: 8px; 
    -moz-border-radius: 8px;
    background:white;
    margin:5px 10px 5px 10px;
    padding:5px;
}
.pagination .pagination_number {
    display:block;
    float:left;
    background:#ffffff;
    border:1px solid #4690d6;
    text-align: center;
    color:#4690d6;
    font-size: 12px;
    font-weight: normal;
    margin:0 6px 0 0;
    padding:0px 4px;
    cursor: pointer;
    -webkit-border-radius: 4px; 
    -moz-border-radius: 4px;
}
.pagination .pagination_number:hover {
    background:#4690d6;
    color:white;
    text-decoration: none;
}
.pagination .pagination_more {
    display:block;
    float:left;
    background:#ffffff;
    border:1px solid #ffffff;
    text-align: center;
    color:#4690d6;
    font-size: 12px;
    font-weight: normal;
    margin:0 6px 0 0;
    padding:0px 4px;
    -webkit-border-radius: 4px; 
    -moz-border-radius: 4px;
}
.pagination .pagination_previous,
.pagination .pagination_next {
    display:block;
    float:left;
    border:1px solid #4690d6;
    color:#4690d6;
    text-align: center;
    font-size: 12px;
    font-weight: normal;
    margin:0 6px 0 0;
    padding:0px 4px;
    cursor: pointer;
    -webkit-border-radius: 4px; 
    -moz-border-radius: 4px;
}
.pagination .pagination_previous:hover,
.pagination .pagination_next:hover {
    background:#4690d6;
    color:white;
    text-decoration: none;
}
.pagination .pagination_currentpage {
    display:block;
    float:left;
    background:#4690d6;
    border:1px solid #4690d6;
    text-align: center;
    color:white;
    font-size: 12px;
    font-weight: bold;
    margin:0 6px 0 0;
    padding:0px 4px;
    cursor: pointer;
    -webkit-border-radius: 4px; 
    -moz-border-radius: 4px;
}


/* ***************************************
    MISC.
*************************************** */

/* profile picture upload n crop page */    
#profile_picture_form {
    height:145px;
}   
#current_user_avatar {
    float:left;
    width:160px;
    height:130px;
    border-right:1px solid #cccccc;
    margin:0 20px 0 0;
}   
#profile_picture_croppingtool {
    border-top: 1px solid #cccccc;
    margin:20px 0 0 0;
    padding:10px 0 0 0;
}   
#profile_picture_croppingtool #user_avatar {
    float: left;
    margin-right: 20px;
}   
#profile_picture_croppingtool #applycropping {

}
#profile_picture_croppingtool #user_avatar_preview {
    float: left;
    position: relative;
    overflow: hidden;
    width: 100px;
    height: 100px;
}   


/* ***************************************
    SETTINGS & ADMIN
*************************************** */
.admin_statistics,
.admin_users_online,
.usersettings_statistics,
.admin_adduser_link,
#add-box,
#search-box,
#logbrowser_search_area {
    -webkit-border-radius: 8px; 
    -moz-border-radius: 8px;
    background:white;
    margin:0 10px 10px 10px;
    padding:10px;
}

.usersettings_statistics h3,
.admin_statistics h3,
.admin_users_online h3,
.user_settings h3,
.notification_methods h3 {
    background:#e4e4e4;
    color:#333333;
    font-size:1.1em;
    line-height:1em;
    margin:0 0 10px 0;
    padding:5px;
    -webkit-border-radius: 4px; 
    -moz-border-radius: 4px;    
}
h3.settings {
    background:#e4e4e4;
    color:#333333;
    font-size:1.1em;
    line-height:1em;
    margin:10px 0 4px 0;
    padding:5px;
    -webkit-border-radius: 4px; 
    -moz-border-radius: 4px;
}

#topbar
{
    width:100%;
    /* height:48px; */
    background:#1d1d1d url("<?php echo $graphicsDir; ?>/topgradient.gif?v5") repeat-x left -1px;
}

#topRight
{
    position:absolute;
    right:0px;
    top:0px;
}

.topbarTable
{
    width:100%;    
}

#topbar form
{
    display:inline;
}

.topbarLinks a
{
    display:block;
    float:left;
    padding:14px 20px 10px 20px;
    border-left:1px solid #5d5d5d;
    border-right:1px solid #2f2f2f;
    height:23px;
    color:#e6e6e6;
}

.topbarLinks a:hover
{
    background:#1d1d1d url("<?php echo $graphicsDir; ?>/topgradient.gif?v5") repeat-x left -49px;  
    color:#e6e6e6;
    text-decoration:none;
}

.topbarLinks a#logoContainer
{
    padding:8px 22px 16px 15px;
    overflow:hidden;
    border-left:0px;
}

.topbarLinks form
{    
    padding-left: 10px;
    padding-right: 10px;
}

#loginButton, #loggedinArea
{    
    width:166px;
    display:block;
}

#loginButton
{
    height:47px;
}

#loginButton
{
    background:#4d4d4d url(<?php echo $graphicsDir; ?>/loginbutton_sm.gif) no-repeat left top;
}

a#loginButton:hover
{
    background-position:left -46px;
}

#loggedinArea
{
    background:url(<?php echo $graphicsDir; ?>/loggedinarea_rounded.gif?v2) no-repeat left top;
}    

a#loginButton:hover 
{
    text-decoration:none;
}

a#loginButton:hover .loginContent span
{
    text-decoration:none;
}    

#loginButton img
{
    margin-right:10px;
    vertical-align:-4px;
}

#loginButton .loginContent
{
    display:block;    
    padding-top:12px;
    text-align:center;
    color:#e6e6e6;
    font-weight:bold;
}

.loggedInAreaContent
{
    display:block;    
    height:30px;
    padding:10px 0px 6px 0px;
    text-align:center;
    color:#e6e6e6;
    font-weight:bold;
}

.loggedInAreaContent a
{
    margin-left:5px;
    margin-right:5px;
}

.loggedInAreaContent a:hover
{
    border-bottom:1px solid black;
}

.dropdown
{
    position:absolute;
    z-index:10;
    left:100px;
    top:100px;
    width:180px;
    background-color:#2b2b2b;
    border:1px solid #b8b8b8;
    padding-bottom:8px;
    -moz-border-radius: 8px;
    -webkit-border-radius: 8px;
    display:none;
}

.dropdown_title
{
    padding:6px;
    font-weight:bold;
    border-bottom:1px solid #545454;
    color:#e6e6e6;
}

.dropdown_item
{
    display:block;
    color:black;
}

.dropdown_item_selected
{
    font-weight:bold;
}

a.dropdown_item:hover
{
    color:black;    
}

.dropdown_content
{   
    background-color:#e6e6e6;
    padding:3px;
}

.thin_column
{
    width:500px;
    margin:0 auto;   
}

.wide_column
{
    background-color:#f8f8f8;
    padding:10px;
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
    width:483px;
    margin:0 auto;
    -webkit-border-radius: 4px; 
    -moz-border-radius: 4px;    
    border:2px solid #1caeec;
}

.swfupload
{
    vertical-align:middle;
}

.uploadIframe
{
    display:block;
    width:270px;
    height:30px;
    border:0px;
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

body
{
    background-color:#e7e2d7;
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

.feed_org_icon
{
    float:left;
    width:50px;
}

.feed_org_icon img
{
    width:40px;
}

.feed_content
{
    float:left;
    width:400px;
}

.feed_more
{
    font-size:10px;
}

.blog_date
{
    color: #aaa;
    font-size:11px;
}

.blog_more
{    
    float:right;
}

.transContributeLink
{
    display:block;
    float:right;
    font-size:9px;
}

.smallBlogImageLink
{
    float:left;
    margin-right:4px;
    margin-bottom:4px; 
    padding:3px;
    background-color:white;
    border: 1px solid #ccc;

}

a.smallBlogImageLink:hover
{
    border: 1px solid #7391a9;
}

.largeBlogImageLink
{
    margin-bottom:10px;
    text-align:center;
    display:block;
}

#blogTimeline
{
    margin:5px 0px 5px 0px;
    position:relative;
    width:460px;
    height:50px;
}

#blogTimelineLeft, #blogTimelineRight, #blogTimelineLine
{
    position:absolute;    
    top:7px;
    height:26px;
}

#blogTimelineLeft
{
    left:18px;   
    width:13px;
    background:url(<?php echo $graphicsDir ?>/timeline.gif) no-repeat left -26px;
}

#blogTimelineRight
{
    left:431px;   
    width:13px;
    background:url(<?php echo $graphicsDir ?>/timeline.gif) no-repeat left -52px;
}

#blogTimelineLine
{
    left:31px;
    width:400px;
    background:url(<?php echo $graphicsDir ?>/timeline.gif) repeat-x left top;
}

.timelineMarker
{
    position:absolute;
    height:4px;
    top:27px;
    width:1px;
    overflow:hidden;
    background-color:#333;
}

.timelineLink
{
    position:absolute;    
    top:13px;
    width:5px;
    height:14px;
    display:block;
    overflow:hidden;
    background-color:#333;
}

.timelineCur
{
    position:absolute;
    height:17px;
    top:-3px;
    width:13px;
    background:url(<?php echo $graphicsDir; ?>/timeline.gif) no-repeat left -87px;
}

.timelineLabel
{
    position:absolute;
    top:30px;
    width:70px;
    text-align:center;
    font-size:10px;
}

#hoverPost
{
    position:absolute;
    top:27px;
}

#hoverPost img
{
    display:block;
    margin:0 auto;
}

#blogNavPrev, #blogNavNext
{
    position:absolute;
    display:block;
    height:19px;
    width:22px;
    top:9px;
}

#blogNavPrev
{
    left:0px;
    background: url(<?php echo $graphicsDir ?>/arrows_sm.gif) no-repeat left top;
}

#blogNavNext
{
    left:440px;
    background: url(<?php echo $graphicsDir ?>/arrows_sm.gif) no-repeat right top;
}

.homeLanguages
{
    text-align: center;
}

.homeHeading
{
    padding:5px 10px 10px 10px;    
    font:16px Arial;
}

.instructions
{
    padding:5px 0px;
}

.searchForm select
{
    font-size:11px;
}

.searchField
{
    width:220px;
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

.widget_image_top
{
    display:block;
    margin:0px auto 6px auto;
}

.widget_image_bottom
{
    display:block;
    margin:6px auto 0px auto;
}

.widget_image_left
{
    float:left;
    margin:0px 6px 6px 0px;
}

.widget_image_right
{
    float:right;
    margin:0px 0px 6px 6px;
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

#translate_bar
{
    margin:0px 1px 3px 1px;
    padding: 6px 2px 8px 51px;
    overflow:visible;
    background:#948f87 url(<?php echo $graphicsDir; ?>/world.gif) no-repeat 5px 5px;
    color:white;
    border:1px solid #fff;
    -webkit-border-radius: 4px; 
    -moz-border-radius: 4px; 
    font-size:12px;
}
#translate_bar a
{
    color:white;
    font-weight:bold;
    white-space:nowrap;
}

.addUpdateButton
{
    float:right;
    margin:4px 0px !important;
}

#attachImage
{
    padding:4px;
    margin-top:2px;
    border:1px solid #ccc;
    -moz-border-radius:6px;
    -webkit-border-radius:6px;
    width:320px;
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

.attachImageClose
{
    float:right;
    margin-left:8px;
    display:block;
    height:14px;
    width:14px;
    background:url(<?php echo $graphicsDir ?>/icon_customise_remove.png) no-repeat left top;
}

a.attachImageClose:hover
{
    background-position:left -16px;
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

.language, .footerLinks
{
    padding:0px 10px 15px 10px;
}

.footerLinks
{
    float:right;
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

.messageTable
{
    width:100%;
}

.messageTable td, .messageTable th
{
    padding:3px;
}

.messageTable th
{
    text-align:right;
}

.messageTable .input-text, .messageTable .input-textarea
{
    width:350px;
}

.contactTable td, .contactTable th
{
    padding:3px;
}

.contactTable th
{
    text-align:right;
}

.team_member_view
{
    padding-bottom:10px;    
}

.team_member_img
{
    float:right;
    padding-left:6px;
}

.team_member_name
{
    font-weight:bold;
}

.search_listing {
    display: block;
    -webkit-border-radius: 8px; 
    -moz-border-radius: 8px;
    background:white;
    margin:0 10px 5px 10px;
    padding:5px;
}
.search_listing_icon {
    float:left;
}
.search_listing_icon img {
    width: 40px;
}
.search_listing_icon .avatar_menu_button img {
    width: 15px;
}
.search_listing_info {
    margin-left: 50px;
    min-height: 40px;
}
/* IE 6 fix */
* html .search_listing_info {
    height:40px;
}
.search_listing_info p {
    margin:0 0 3px 0;
    line-height:1.2em;
}
.search_listing_info p.owner_timestamp {
    margin:0;
    padding:0;
    color:#666666;
    font-size: 90%;
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

.edited, .edited label
{
    color:#cc6600;
}

.reviewed
{
    color:#00aa00;
}

.mapMarker
{
    position:absolute;
    cursor:pointer;
}

.mapMarkerCount
{
    position:absolute;
    left:0px;
    top:2px;
    width:22px;
    font-weight:bold;
    text-align:center;    
}

#infoOverlay
{
    position:absolute;
    padding:5px;
    background:white;
    border:1px solid #ccc;
    -moz-border-radius:4px;
    -webkit-border-radius:4px;
    left:0px;
    top:0px;
    display:none;
}

.mapOrgLink
{
    font-weight:bold;
    display:block;
    white-space:nowrap;
}