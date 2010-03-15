<?php
    $graphicsDir = $vars['url'] . "_graphics";
?>

#topbar
{
    width:100%;
    height:50px;
    background:#d9d9d9 url("<?php echo $graphicsDir; ?>/topgradient.gif") repeat-x left top;  
}

#topbarTable
{
    width:100%;
}

#topbarTable td
{
    padding:15px;
}

#topbarTable td#logoContainer
{
    padding:3px;
    width:180px;
}

#topbar form
{
    display:inline;
}

.topbarLinks a, form
{
    padding-left: 10px;
    padding-right: 10px;
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

.blog_post
{
    clear:both;
    padding-bottom:10px;
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

.blog_post p 
{
    margin: 0 0 5px 0;
}

.blog_post .strapline 
{
    margin: 0 0 0 35px;
    padding:0;
    color: #aaa;
    line-height:1em;
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
    margin-right:5px;
    margin-bottom:10px;
}

.largeBlogImageLink
{
    margin-bottom:10px;
    text-align:center;
    display:block;
}

.blogNav
{
    text-align:center;
    padding-bottom:10px;
}

#blogTimeline
{
    margin:0px 15px;
    position:relative;
    width:420px;
    height:45px;
}

#blogTimelineLine
{
    position:absolute;
    left:0px;
    top:10px;
    height:1px;
    width:400px;
    background-color:gray;
}

.timelineLink
{
    position:absolute;    
}

.timelineCur
{
    position:absolute;
    height:20px;
    top:0px;
    width:2px;
    background-color:gray;
}

.timelineMarker
{
    position:absolute;
    height:20px;
    top:0px;
    width:1px;
    background-color:gray;
}

.timelineLabel
{
    position:absolute;
    top:21px;
    width:70px;
    text-align:center;
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