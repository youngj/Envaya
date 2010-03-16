<?php
    $graphicsDir = $vars['url'] . "_graphics";
?>

#topbar
{
    width:100%;
    /* height:48px; */
    background:#333 url("<?php echo $graphicsDir; ?>/topgradient.gif?v2") repeat-x left top;  
}

#topbarTable
{
    width:100%;
}

#topbarTable td
{
    padding-top:1px;
    padding-bottom:1px;
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
    height:22px;
    color:#e6e6e6;
}

.topbarLinks a#logoContainer
{
    padding:8px 22px 16px 15px;
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
    height:46px;
    display:block;
    float:right;
}

#loginButton
{
    background:#4d4d4d url(<?php echo $graphicsDir; ?>/loginbutton_sm.gif) no-repeat left top;
}

#loggedinArea
{
    background:#1d5153 url(<?php echo $graphicsDir; ?>/loggedinarea_sm.gif) no-repeat left top;
}    

a#loginButton:hover 
{
    text-decoration:none;
}

a#loginButton:hover .loginContent span
{
    text-decoration:underline;
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
    padding-top:12px;
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

#thin_column
{
    width:493px;
    margin:0 auto;   
}

#sidebar_container
{
    position:absolute;
    top:30px;
    margin-left:493px;
    width:150px;
    height:50px;
}

#sidebar_container ul
{
    margin:0px;
    padding:0px;
}

#sidebar_container li.selected a
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
    padding:15px;
    color:black;    
    letter-spacing:1px;
    font-family:"Gill Sans MT", sans-serif;
}

#heading img
{
    float:left;
    padding-right:10px;
}

#heading h1
{
    color:#222;
    font-size:22px;
    padding-top:14px;
    padding-bottom:0px;
    margin:0px;
}

#heading h1.withicon
{
    padding-top:20px;
}

#heading h1.withouticon,
#heading h2.withouticon
{
    text-align:center;
}

#heading h2
{
    color:#222;
    font-size:14px;
    padding:0px;
    margin:0px;
}

#content
{
    clear:both;
    padding-top:10px;
    position:relative;
}

#content_top
{
    height:24px;
    background:#fff url("<?php echo $graphicsDir; ?>/contenttop.gif") no-repeat left top;  
}

#content_bottom
{
    height:24px;
    background:#fff url("<?php echo $graphicsDir; ?>/contentbottom.gif") no-repeat left top;  
}

#content_mid
{
    background:#fff url("<?php echo $graphicsDir; ?>/contentgradient.gif") repeat-y left top;  
    padding:0px 6px;
}

.section_header
{
    position:relative;
    clear:both;
    background:#e6e6e6 url("<?php echo $graphicsDir; ?>/sectionheader.gif") no-repeat left top;  
    height:21px;
    padding:10px 15px;
    font-family:"Gill Sans MT", sans-serif;
    text-transform:uppercase;
    font-weight:bold;
    font-size:14px;
}

.section_content
{
    padding:5px 10px;
}

.sidebar_link
{
    position:absolute;
    left:490px;
}

.good_messages, .bad_messages 
{
    background:#ccffcc;
    color:#000000;
    padding:3px;
    width:483px;
    margin:3px auto;
    -webkit-border-radius: 4px; 
    -moz-border-radius: 4px;
    border:2px solid #00CC00;
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
    margin:0px 10px;
}

.blog_post
{
    clear:both;
    margin-bottom:10px;
}

.blog_post p 
{
    margin: 0 0 5px 0;
}

.blog_date
{
    color: #aaa;
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
}

.smallBlogImageLink
{
    border: 1px solid transparent;
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
    margin:0px 15px;
    position:relative;
    width:420px;
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
    left:0px;   
    width:13px;
    background:url(<?php echo $graphicsDir ?>/timeline.gif) no-repeat left -26px;
}

#blogTimelineRight
{
    left:413px;   
    width:13px;
    background:url(<?php echo $graphicsDir ?>/timeline.gif) no-repeat left -52px;
}

#blogTimelineLine
{
    left:13px;
    width:400px;
    background:url(<?php echo $graphicsDir ?>/timeline.gif) repeat-x left top;
}

.timelineMarker
{
    position:absolute;
    height:4px;
    top:27px;
    width:1px;
    background-color:#333;
}

.timelineLink
{
    position:absolute;    
    top:13px;
    width:5px;
    height:14px;
    display:block;
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
    left:-20px;
    background: url(<?php echo $graphicsDir ?>/arrows_sm.gif) no-repeat left top;
}

#blogNavNext
{
    left:425px;
    background: url(<?php echo $graphicsDir ?>/arrows_sm.gif) no-repeat right top;
}

.homeLanguages
{
    text-align: center;
}

.homeHeading
{
    padding:10px;    
    font:16px Arial;
}

.homeSection
{
    padding:5px;
    clear:both;
}

.homeSectionIcon
{
    float:left;
    margin-right:10px;
    border:1px solid #666699;
}

.homeSubheading
{
    font:bold 15px Arial;
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

.progressTabs
{
    padding:5px;
}

.progressTabs li
{
    float:left;
    padding:7px;
    color:#666;
    background-color:#ddd;
    border:1px solid #ddd;
}

.progressTabs li.active
{
    border:1px solid #666;
    background-color:#eee;
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

.widget_disabled
{
    color:gray;
}

#widget_delete
{
    float:right;
    background-color:#990000;
    border-color:#660000;
}

#widget_delete:hover
{
    background-color:#aa0000;
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

.editor
{
    background-color:#4c4c4c;
}

.editor #heading h1
{
    color:#e6e6e6;
}

.editor #content_mid
{
    background:#fff url("<?php echo $graphicsDir; ?>/editgradient.gif") repeat-y left top;  
}

.editor #content_top
{
    height:9px;
    background:#fff url("<?php echo $graphicsDir; ?>/edittop.gif") no-repeat left top;  
}

.editor #content_bottom
{
    background:#fff url("<?php echo $graphicsDir; ?>/editgradient.gif") repeat-y left top;  
}

.adminBox
{
    position:absolute;
    top:55px;
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
    padding: 5px 2px 5px 36px;
    height:32px;
    background:#fdfdfd url(<?php echo $graphicsDir; ?>/world.gif) no-repeat 5px 8px;
    border-bottom:1px solid #ccc;
    margin-bottom:2px;
    font-size:11px;
}
#translate_bar a
{
    white-space:nowrap;
}