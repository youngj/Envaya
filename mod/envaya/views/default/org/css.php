<?php

    /**
     * Elgg Groups css
     *
     * @package groups
     * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
     * @author Curverider Ltd <info@elgg.com>
     * @copyright Curverider Ltd 2008-2009
     * @link http://elgg.com/
     */

    $graphicsDir = $vars['url'] . "mod/envaya/graphics";
?>

#topbar
{
    height:50px;
    background:#d9d9d9 url("<?php echo $graphicsDir; ?>/topgradient.gif") repeat-x left top;  
}

#topbar form
{
    display:inline;
}

#logo_container
{
    padding:3px;
    float:left;
}

#topbar_container_left
{
    padding:15px;
    float:left;
}


#topbar_container_left a, form
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
    width:120px;
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
    padding:30px;
    text-align:center;
    text-transform:uppercase;
    color:black;    
    letter-spacing:1px;
    font-family:"Gill Sans MT", sans-serif;
}

#heading h1
{
    font-size:22px;
}


#heading h2
{
    color:#222;
    font-size:14px;
}

#content
{
    background-color:#fff;
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

.org_website
{
    float:right;
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

#layout_header, #layout_header a
{
    color:white;
}

#layout_canvas
{
    margin: 0 auto;
    width:483px;
}

#elgg_topbar_container_search
{
    right:20px;
}

.padded
{
    margin:0px 10px;
}

#content_area_group_title h2 {
    color:#0054A7;
    font-size:1.35em;
    line-height:1.2em;
    margin:0 0 0 8px;
    padding:5px;
}
#topic_posts #content_area_group_title h2 {
    margin:0 0 0 0;
}

#two_column_left_sidebar_maincontent #owner_block_content {
    margin:0 0 10px 0 !important;
}

#groups_info_column_left {
    float:left:
    width:435px;
    margin-left:230px;
    margin-right:10px;
}

#groups_info_column_left .odd {
    background:#E9E9E9;
    -webkit-border-radius: 5px;
    -moz-border-radius: 5px;
}
#groups_info_column_left .even {
    background:#E9E9E9;
    -webkit-border-radius: 5px;
    -moz-border-radius: 5px;
}
#groups_info_column_left p {
    margin:0 0 7px 0;
    padding:2px 4px;
}

#groups_info_column_right {
    float:left;
    width:230px;
    margin:0 0 0 10px;
}
#groups_info_wide p {
    text-align: right;
    padding-right:10px;
}
#group_stats {
    width:190px;
    background: #e9e9e9;
    padding:5px;
    margin:10px 0 20px 0;
    -webkit-border-radius: 5px;
    -moz-border-radius: 5px;
}
#group_stats p {
    margin:0;
}
#group_members {
    margin:10px;
    -webkit-border-radius: 8px;
    -moz-border-radius: 8px;
    background: white;
}

#right_column {
    clear:left;
    float:right;
    width:340px;
    margin:0 10px 0 0;
}
#left_column {
    width:340px;
    float:left;
    margin:0 10px 0 10px;

}
/* IE 6 fixes */
* html #left_column {
    margin:0 0 0 5px;
}
* html #right_column {
    margin:0 5px 0 0;
}

#group_members h2,
#right_column h2,
#left_column h2,
#fullcolumn h2 {
    margin:0 0 10px 0;
    padding:5px;
    color:#0054A7;
    font-size:1.25em;
    line-height:1.2em;
}
#fullcolumn .contentWrapper {
    margin:0 10px 20px 10px;
    padding:0 0 5px;
}

.member_icon {
    margin:0 0 6px 6px;
    float:left;
}

/* IE6 */
* html #topic_post_tbl { width:676px !important;}

/* all browsers - force tinyMCE on edit comments to be full-width */
.edit_forum_comments .defaultSkin table.mceLayout {
    width: 636px !important;
}

/* topics overview page */
#forum_topics {
    padding:10px;
    margin:0 10px 0 10px;
    background:white;
    -webkit-border-radius: 8px;
    -moz-border-radius: 8px;
}
/* topics individual view page */
#topic_posts {
    margin:0 10px 5px 10px;
}
#topic_posts #pages_breadcrumbs {
    margin:2px 0 0 0px;
}
#topic_posts form {
    padding:10px;
    margin:30px 0 0 0;
    background:white;
    -webkit-border-radius: 8px;
    -moz-border-radius: 8px;
}
.topic_post {
    padding:10px;
    margin:0 0 5px 0;
    background:white;
    -webkit-border-radius: 8px;
    -moz-border-radius: 8px;
}
.topic_post .post_icon {
    float:left;
    margin:0 8px 4px 0;
}
.topic_post h2 {
    margin-bottom:20px;
}
.topic_post p.topic-post-menu {
    margin:0;
}
.topic_post p.topic-post-menu a.collapsibleboxlink {
    padding-left:10px;
}
.topic_post table, .topic_post td {
    border:none;
}

/* group latest discussions widget */
#latest_discussion_widget {
    margin:0 0 20px 0;
    background:white;
    -webkit-border-radius: 8px;
    -moz-border-radius: 8px;
}
/* group files widget */
#filerepo_widget_layout {
    margin:0 0 20px 0;
    padding: 0 0 5px 0;
    background:white;
    -webkit-border-radius: 8px;
    -moz-border-radius: 8px;
}
/* group pages widget */
#group_pages_widget {
    margin:0 0 20px 0;
    padding: 0 0 5px 0;
    background:white;
    -webkit-border-radius: 8px;
    -moz-border-radius: 8px;
}
#group_pages_widget .search_listing {
    border: 2px solid #cccccc;
}
#right_column .filerepo_widget_singleitem {
    background: #dedede !important;
    margin:0 10px 5px 10px;
}
#left_column .filerepo_widget_singleitem {
    background: #dedede !important;
    margin:0 10px 5px 10px;
}
.forum_latest {
    margin:0 10px 5px 10px;
    background: #dedede;
    padding:5px;
    -webkit-border-radius: 4px;
    -moz-border-radius: 4px;
}
.forum_latest:hover {

}
.forum_latest .topic_owner_icon {
    float:left;
}
.forum_latest .topic_title {
    margin-left:35px;
}
.forum_latest .topic_title p {
    line-height: 1.0em;
    padding:0;
    margin:0;
    font-weight: bold;
}
.forum_latest p.topic_replies {
    padding:3px 0 0 0;
    margin:0;
    color:#666666;
}
.add_topic {
    -webkit-border-radius: 8px;
    -moz-border-radius: 8px;
    background:white;
    margin:5px 10px;
    padding:10px 10px 10px 6px;
}

a.add_topic_button {
    font: 12px/100% Arial, Helvetica, sans-serif;
    font-weight: bold;
    color: white;
    background:#4690d6;
    border:none;
    -webkit-border-radius: 5px;
    -moz-border-radius: 5px;
    width: auto;
    height: auto;
    padding: 3px 6px 3px 6px;
    margin:0;
    cursor: pointer;
}
a.add_topic_button:hover {
    background: #0054a7;
    color:white;
    text-decoration: none;
}



/* latest discussion listing */
.latest_discussion_info {
    float:right;
    width:300px;
    text-align: right;
    margin-left: 10px;
}
.groups .search_listing br {
    height:0;
    line-height:0;
}
span.timestamp {
    color:#666666;
    font-size: 90%;
}
.latest_discussion_info .timestamp {
    font-size: 0.85em;
}
/* new groups page */
.groups .search_listing {
    border:2px solid #cccccc;
    margin:0 0 5px 0;
}
.groups .search_listing:hover {
    background:#dedede;
}
.groups .group_count {
    font-weight: bold;
    color: #666666;
    margin:0 0 5px 4px;
}
.groups .search_listing_info {
    color:#666666;
}
.groupdetails {
    float:right;
}
.groupdetails p {
    margin:0;
    padding:0;
    line-height: 1.1em;
    text-align: right;
}
#groups_closed_membership {
    margin:0 10px 20px 10px;
    padding: 3px 5px 5px 5px;
    background:#bbdaf7;
    -webkit-border-radius: 8px;
    -moz-border-radius: 8px;
}
#groups_closed_membership p {
    margin:0;
}

/* groups membership widget */
.groupmembershipwidget .contentWrapper {
    margin:0 10px 5px 10px;
}
.groupmembershipwidget .contentWrapper .groupicon {
    float:left;
    margin:0 10px 0 0;
}
.groupmembershipwidget .search_listing_info p {
    color: #666666;
}
.groupmembershipwidget .search_listing_info span {
    font-weight: bold;
}

/* groups sidebar */
.featuredgroups .contentWrapper {
    margin:0 0 10px 0;
}
.featuredgroups .contentWrapper .groupicon {
    float:left;
    margin:0 10px 0 0;
}
.featuredgroups .contentWrapper p {
    margin: 0;
    line-height: 1.2em;
    color:#666666;
}
.featuredgroups .contentWrapper span {
    font-weight: bold;
}
#groupssearchform {
    border-bottom: 1px solid #cccccc;
    margin-bottom: 10px;
}
#groupssearchform input[type="submit"] {
    padding:2px;
    height:auto;
    margin:4px 0 5px 0;
}
.sidebarBox #owner_block_submenu {
    margin:5px 0 0 0;
}

/* delete post */
.delete_discussion {

}
.delete_discussion a {
    display:block;
    float:right;
    cursor: pointer;
    width:14px;
    height:14px;
    margin:0;
    background: url("<?php echo $vars['url']; ?>_graphics/icon_customise_remove.png") no-repeat 0 0;
}
.delete_discussion a:hover {
    background-position: 0 -16px;
    text-decoration: none;
}
/* IE6 */
* html .delete_discussion a { font-size: 1px; }
/* IE7 */
*:first-child+html .delete_discussion a { font-size: 1px; }

/* delete group button */
#delete_group_option input[type="submit"] {
    background:#dedede;
    border-color:#dedede;
    color:#333333;
    margin:0;
    float:right;
    clear:both;
}
#delete_group_option input[type="submit"]:hover {
    background:red;
    border-color:red;
    color:white;
}

#groupsearchform .search_input {
    width:176px;
}


.singleview {
    margin-top:10px;
}

.blog_post_icon {
    float:left;
    margin:3px 0 0 0;
    padding:0;
}

.blog_post h3 {
    font-size: 150%;
    margin:0 0 10px 0;
    padding:0;
}

.blog_post h3 a {
    text-decoration: none;
}

.blog_post p {
    margin: 0 0 5px 0;
}

.blog_post .strapline {
    margin: 0 0 0 35px;
    padding:0;
    color: #aaa;
    line-height:1em;
}
.blog_post p.tags {
    background:transparent url(<?php echo $vars['url']; ?>_graphics/icon_tag.gif) no-repeat scroll left 2px;
    margin:0 0 7px 35px;
    padding:0pt 0pt 0pt 16px;
    min-height:22px;
}
.blog_post .options {
    margin:0;
    padding:0;
}

.blog_post_body img[align="left"] {
    margin: 10px 10px 10px 0;
    float:left;
}
.blog_post_body img[align="right"] {
    margin: 10px 0 10px 10px;
    float:right;
}
.blog_post_body img {
    margin: 10px !important;
}

.blog-comments h3 {
    font-size: 150%;
    margin-bottom: 10px;
}
.blog-comment {
    margin-top: 10px;
    margin-bottom:20px;
    border-bottom: 1px solid #aaaaaa;
}
.blog-comment img {
    float:left;
    margin: 0 10px 0 0;
}
.blog-comment-menu {
    margin:0;
}
.blog-comment-byline {
    background: #dddddd;
    height:22px;
    padding-top:3px;
    margin:0;
}
.blog-comment-text {
    margin:5px 0 5px 0;
}

/* New blog edit column */
#blog_edit_page {
    /* background: #bbdaf7; */
    margin-top:-10px;
}
#blog_edit_page #content_area_user_title h2 {
    background: none;
    border-top: none;
    margin:0 0 10px 0px;
    padding:0px 0 0 0;
}
#blog_edit_page #blog_edit_sidebar #content_area_user_title h2 {
    background:none;
    border-top:none;
    margin:inherit;
    padding:0 0 5px 5px;
    font-size:1.25em;
    line-height:1.2em;
}
#blog_edit_page #blog_edit_sidebar {
    margin:0px 0 22px 0;
    background: #dedede;
    padding:5px;
    -webkit-border-radius: 8px; 
    -moz-border-radius: 8px;
    border-bottom:1px solid #cccccc;
    border-right:1px solid #cccccc;
}
#blog_edit_page #two_column_left_sidebar_210 {
    width:210px;
    margin:0px 0 20px 0px;
    min-height:360px;
    float:left;
    padding:0;
}
#blog_edit_page #two_column_left_sidebar_maincontent {
    margin:0 0px 20px 20px;
    padding:10px 20px 20px 20px;
    width:670px;
    background: #bbdaf7;
}
/* unsaved blog post preview */
.blog_previewpane {
    border:1px solid #D3322A;
    background:#F7DAD8;
    padding:10px;
    margin:10px;
    -webkit-border-radius: 8px; 
    -moz-border-radius: 8px;    
}
.blog_previewpane p {
    margin:0;
}

#blog_edit_sidebar .publish_controls,
#blog_edit_sidebar .blog_access,
#blog_edit_sidebar .publish_options,
#blog_edit_sidebar .publish_blog,
#blog_edit_sidebar .allow_comments,
#blog_edit_sidebar .categories {
    margin:0 5px 5px 5px;
    border-top:1px solid #cccccc;
}
#blog_edit_page ul {
    padding-left:0px;
    margin:5px 0 5px 0;
    list-style: none;
}
#blog_edit_page p {
    margin:5px 0 5px 0;
}
#blog_edit_page #two_column_left_sidebar_maincontent p {
    margin:0 0 15px 0;
}
#blog_edit_page .publish_blog input[type="submit"] {
    font-weight: bold;
    padding:2px;
    height:auto;
}
#blog_edit_page .preview_button a {
    font: 12px/100% Arial, Helvetica, sans-serif;
    font-weight: bold;
    background:white;
    border: 1px solid #cccccc;
    color:#999999;
    -webkit-border-radius: 4px; 
    -moz-border-radius: 4px;
    width: auto;
    height: auto;
    padding: 3px;
    margin:1px 1px 5px 10px;
    cursor: pointer;
    float:right;
}
#blog_edit_page .preview_button a:hover {
    background:#4690D6;
    color:white;
    text-decoration: none;
    border: 1px solid #4690D6;
}
#blog_edit_page .allow_comments label {
    font-size: 100%;
}

.transSource
{
    color:#333;
    font:11px Arial;
}

.transContributeLink
{
    display:block;
    float:right;
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

.blogEditControls
{
    float:right;
    font-size:11px;
}