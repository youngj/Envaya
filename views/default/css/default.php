<?php
    if (!isset($contentWidth))
    {
        $contentWidth = 600;
    }
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

<?php    
    include(__DIR__."/base.php");     
?>

th
{
    font-weight:bold;
}

em, i {
    font-style:italic;
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

small {
    font-size: 90%;
}
h1 { font-size: 1.8em;  }
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


#topbar
{
    width:100%;
    background:#1d1d1d url("<?php echo $graphicsDir; ?>/topgradient_sm.gif?v7") repeat-x left -1px;
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

.pagination {
    margin:5px 10px 5px 10px;
    padding:5px;
}
.pagination .pagination_number {
    display:block;
    float:left;
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

<?php
    $timelineWidth = $contentWidth - 60;
?>

#blogTimeline
{
    margin:0px 0px 5px 0px;
    position:relative;
    width:<?php echo $timelineWidth ?>px;
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
    left:<?php echo $timelineWidth - 29 ?>px;
    width:13px;
    background:url(<?php echo $graphicsDir ?>/timeline.gif) no-repeat left -52px;
}

#blogTimelineLine
{
    left:31px;
    width:<?php echo $timelineWidth - 60 ?>px;
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
    left:<?php echo $timelineWidth - 20 ?>px;
    background: url(<?php echo $graphicsDir ?>/arrows_sm.gif) no-repeat right top;
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

.topbarLinks
{
    font-size:12px;
}

.topbarLinks a
{
    display:block;
    float:left;
    padding:6px 18px 4px 18px;
    border-left:1px solid #5d5d5d;
    border-right:1px solid #2f2f2f;
    height:19px;
    color:#e6e6e6;
}

.topbarLinks a:hover
{
    background:#1d1d1d url("<?php echo $graphicsDir; ?>/topgradient_sm.gif?v7") repeat-x left -30px;
    color:#e6e6e6;
    text-decoration:none;
}

.topbarLinks a#logoContainer
{
    padding:2px 10px 8px 10px;
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
    width:159px;
    display:block;
}

#loginButton
{
    height:29px;
}

#loginButton
{
    background:#4d4d4d url(<?php echo $graphicsDir; ?>/loginbutton_sm.gif?v4) no-repeat left top;
}

a#loginButton:hover
{
    background-position:left -41px;
}

#loggedinArea
{
    background:url(<?php echo $graphicsDir; ?>/loggedinarea_rounded.gif?v2) no-repeat left -18px;
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
    padding-top:3px;
    text-align:center;
    color:#e6e6e6;
    font-weight:bold;    
}

.loggedInAreaContent
{
    display:block;
    height:26px;
    padding:1px 0px 2px 0px;
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

.dropdown_content
{
    background-color:#e6e6e6;
    padding:3px;
}

.thin_column
{
    width:<?php echo $contentWidth ? $contentWidth."px" : 'auto' ?>;
    margin:0 auto;
}

.wide_column
{
    background-color:#f8f8f8;
    padding:10px;
}

.input-text,
.input-tags,
.input-url,
.input-textarea {
    width:<?php echo $contentWidth ? ($contentWidth - 52)."px" : "auto" ?>;
    margin-top:4px;
}

.input-textarea {
    height: 200px;
    margin-top:4px;
}

.input-file {
    margin-top:4px;
}

.modalShadow
{
    position:absolute;
    z-index:100;
    background-color:gray;
    opacity:0.5;
    filter:alpha(opacity=50);
    width:100%;
    height:100%;
    left:0px;
    top:0px;
}

.modalBox
{
    position:absolute;
    z-index:101;
    border:5px solid #3E5A77;
    background-color:white;
    left:200px;
    top:200px;
}

.linkUrl .input-text, .linkText .input-text
{
    width:350px;
}

.modalButtons
{
    padding:0px 15px 8px 15px;
}

.modalHeading
{
    background-color:#B9C7D2;
    color:#021324;
    font:bold 16px arial,sans-serif;
    padding:10px 15px;
}

.modalBody
{
    padding:8px 15px 0px 15px;
}

label {
    font-size: 120%;
}
.sub_input label
{
    font-size:100%;
}

.input-text, .input-textarea, .input-password {
    font: 120% Arial, Helvetica, sans-serif;
    padding: 5px;
    border: 1px solid #cccccc;
    color:#666666;
    -webkit-border-radius: 5px;
    -moz-border-radius: 5px;
}
.input-textarea {
    font: 120% Arial, Helvetica, sans-serif;
    border: solid 1px #cccccc;
    padding: 5px;
    color:#666666;
    -webkit-border-radius: 5px;
    -moz-border-radius: 5px;
}
.input-textarea:focus, .input-text:focus {
    border: solid 1px #4690d6;
    background: #e4ecf5;
    color:#333333;
}

.searchField
{
    width:220px;
}

.feed_content
{
    float:left;
    width:<?php echo $contentWidth - 95 ?>px;
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

.good_messages, .bad_messages
{
    width:<?php echo $contentWidth ? ($contentWidth - 17)."px" : "auto" ?>;
    -webkit-border-radius: 4px;
    -moz-border-radius: 4px;
    margin:0 auto;
    
}

.todo_container .good_messages
{
    padding:12px;
    width:<?php echo $contentWidth ? "565px" : 'auto'; ?>;
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
    background: #08c url(<?php echo $graphicsDir ?>/buttons.gif?v3) left -32px;
    display:block;
    height:32px;
    margin:0px;
    padding-left:9px;
}

.submit_button span
{
    background: #08c url(<?php echo $graphicsDir ?>/buttons.gif?v3) right -32px; 
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

#loadingOverlay
{
    position:absolute;
    padding:5px;
    background:white;
    border:1px solid #ccc;
    display:none;
}

.mapOrgLink
{
    white-space:nowrap;
}

.photoPreviewContainer
{
    padding:5px;
}

.photoPreview
{
    display:block;
    float:left;
    width:170px;
}

.photoCaptionInput
{
    display:block;
    float:left;
    width:280px;
    height:70px;
}

.photoDelete
{
    display:block;
    float:left;
    margin-left:5px;
    width:29px;
    height:29px;
    background:url("<?php echo $graphicsDir ?>/delete.gif?v2") no-repeat left -30px;
}

a.photoDelete:hover
{
    background-position:left top;
}

.mapBucketControls
{
    font-weight:bold;
    width:350px;
}

.uploadIframe
{
    display:inline;
    vertical-align:top;
    width:270px;
    height:30px;
    border:0px;
}

.modalImageFrame
{
    width:370px;
    height:1px;
}

.modalDocumentFrame
{
    width:600px;
    height:1px;
}

.modalImageFrameLoading
{
    height:49px;
}


#donate_form .input
{
    padding:0px 0px 5px 0px;
    font-size:12px;
}

#donate_form .input-text
{
    width:230px;
}

#donate_form input
{
    font-size:12px;
}

#donor_address
{
    padding-left:30px;
}
#donor_address input
{
    width:200px;
}

#donation_amount label
{
    font-weight:normal;
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

.top_language
{
    float:right;
    padding:3px 10px 2px 5px;
    color:white;
    color:#e6e6e6;
    white-space:nowrap;
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

.login-input
{
    width:200px;
}

.paddedTable td, .paddedTable th
{
    padding:5px;
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


#comment_form textarea
{
    height:70px;
    width:<?php echo ($contentWidth - 70) ?>px;
}

#comment_form th
{
    text-align:right;
    vertical-align:middle;
    padding-right:10px;
}   

#comment_form label
{
    font-size:100%;
}

.comment_name_input
{
    width:250px;
}
.comment_name
{
    font-weight:bold;
}

.comment_link
{
	font-size:11px;
	padding-top:3px;
}

.swfupload
{
    vertical-align:top;
}

#load_more
{
    text-align:center;
    padding-top:5px;
    height:30px;
}

#viewtype
{
    float:right;
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

.noBorderTable th, .noBorderTable td
{
    border:0px;
}