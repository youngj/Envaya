<?php
    if (!isset($vars['contentWidth']))
    {
        $vars['contentWidth'] = 600;
    }    
    
    $contentWidth = $vars['contentWidth'];
    
    echo view('css/snippets/reset', $vars);
    echo view('css/base', $vars);
    echo view('css/snippets/topbar', $vars);
?>

#translate_bar
{
    margin:0px 1px 3px 1px;
    padding: 7px;
    background-color:#948f87;
    overflow:visible;
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

.post_nav
{
    position:relative;
    height:20px;
    width:<?php echo $contentWidth - 42; ?>px;
}

.post_nav a
{
    position:absolute;
    display:block;
    top:5px;
    width:120px;
}

.post_nav_prev
{
    left:0px;
}

.post_nav_next
{
    right:0px;
    text-align:right;
}

.thin_column
{
    width:<?php echo $contentWidth ? $contentWidth."px" : 'auto' ?>;
    margin:0 auto;
}

.wide_column
{
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

.modalClose
{
    display:block;
    float:right;
    width:23px;
    height:23px;
    cursor:pointer;
    background:url("/_graphics/delete.gif?v2") no-repeat left -33px;    
}
.modalClose:hover
{
    background-position:left -3px;
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
    clear:both;
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
    background: #08c url(/_graphics/buttons.png) left -32px;
    display:block;
    height:32px;
    margin:0px;
    padding-left:9px;
}

.submit_button span
{
    background: #08c url(/_graphics/buttons.png) right -32px; 
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
    background:url("/_graphics/delete.gif?v2") no-repeat left -30px;
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

.tabs .active
{
    font-weight:bold;
}

.header_icons
{
    height:30px;
    width:36px;
    margin:0 auto;
    background:url(/_graphics/move_edit_delete.gif) no-repeat left top;
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
    background:url(/_graphics/commBox.gif) no-repeat right -10px;
    width:45%;
}

.commBoxMain
{
    background:url(/_graphics/commBox.gif) repeat-x left -56px;
    white-space:nowrap;
    padding:5px 15px;
}

.commBoxRight
{
    background:url(/_graphics/commBox.gif) no-repeat left -102px;
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

#comment_form textarea
{
    height:70px;
    width:<?php echo ($contentWidth - 70) ?>px;
}

.inputTable th
{
    text-align:right;
    vertical-align:middle;
    padding-right:10px;
}   

.inputTable label
{
    font-size:100%;
}

.inputTable input
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

.gridDelete, .hideMessages
{
    display:block;
    width:30px;
    height:20px;
    background:url("/_graphics/delete.gif?v2") no-repeat left -35px;
}

a.gridDelete:hover, a.hideMessages:hover
{
    background-position:left -5px;
}

.deleteSection
{
    cursor:pointer;
    border: 0px;
    width:22px;
}


.hideMessages
{
    float:right; 
    width:22px;
    margin-top:3px;
}   

.noBorderTable th, .noBorderTable td
{
    border:0px;
}

.discussionTopic
{
    display:block;
    padding:4px;
    border:1px solid transparent;
}

a.discussionTopic:hover 
{
    border:1px solid #ccc;
}

a.discussionTopic
{
    text-decoration:none;
}

.follow_icon
{
    background:url(/_graphics/home/sprite.png) no-repeat left top;
    display:block;
    float:left;
    width:36px;
    height:36px;
    top:0px;
    position:absolute;
}

.shareLinks
{
    padding-top:5px;
    padding-left:5px;
    white-space:nowrap;
}

.shareLinks a
{
    display:block;
    clear:both;
    opacity:0.7;
    text-align:right;
    font-size:10px;    
    padding-top:1px;
    padding-right:23px;
    background:url(/_graphics/share2.png) no-repeat right top;
    height:19px;
}

.shareLinks a:hover
{
    opacity:1.0;
}
