/* canvas layout: 2 column left sidebar */
#two_column_left_sidebar {
    width:210px;
    margin:0 20px 0 0;
    min-height:360px;
    float:left;
    background: #dedede;
    padding:0px;
    -webkit-border-radius: 8px; 
    -moz-border-radius: 8px;
    border-bottom:1px solid #cccccc;
    border-right:1px solid #cccccc;
}

#two_column_left_sidebar_maincontent {
    width:718px;
    margin:0;
    min-height: 360px;
    float:left;
    background: #dedede;
    padding:0 0 5px 0;
    -webkit-border-radius: 8px; 
    -moz-border-radius: 8px;
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

#owner_block {
    padding:10px;
}
#owner_block_icon {
    float:left;
    margin:0 10px 0 0;
}
#owner_block_rss_feed,
#owner_block_odd_feed,
#owner_block_bookmark_this,
#owner_block_report_this {
    padding:5px 0 0 0;
}
#owner_block_report_this {
    padding-bottom:5px;
    border-bottom:1px solid #cccccc;
}
#owner_block_rss_feed a {
    font-size: 90%;
    color:#999999;
    padding:0 0 4px 20px;
    background: url(<?php echo $vars['url']; ?>_graphics/icon_rss.gif) no-repeat left top;
}
#owner_block_rss_feed a:hover {
    color: #0054a7;
}
#owner_block_desc {
    padding:4px 0 4px 0;
    margin:0 0 0 0;
    line-height: 1.2em;
    border-bottom:1px solid #cccccc;
    color:#666666;
}
#owner_block_content {
    margin:0 0 4px 0;
    padding:3px 0 0 0;
    min-height:35px;
    font-weight: bold;
}
#owner_block_content a {
    line-height: 1em;
}
.ownerblockline {
    padding:0;
    margin:0;
    border-bottom:1px solid #cccccc;
    height:1px;
}
#owner_block_submenu {
    margin:20px 0 20px 0;
    padding: 0;
    width:100%;
}
#owner_block_submenu ul {
    list-style: none;
    padding: 0;
    margin: 0;
}
#owner_block_submenu ul li.selected a {
    background: #4690d6;
    color:white;
}
#owner_block_submenu ul li.selected a:hover {
    background: #4690d6;
    color:white;
}
#owner_block_submenu ul li a {
    text-decoration: none;
    display: block;
    margin: 2px 0 0 0;
    color:#4690d6;
    padding:4px 6px 4px 10px;
    font-weight: bold;
    line-height: 1.1em;
    -webkit-border-radius: 10px; 
    -moz-border-radius: 10px;
}
#owner_block_submenu ul li a:hover {
    color:white;
    background: #0054a7;
}

/* IE 6 + 7 menu arrow position fix */
* html #owner_block_submenu ul li.selected a {
    background-position: left 10px;
}
*:first-child+html #owner_block_submenu ul li.selected a {
    background-position: left 8px;
}

#owner_block_submenu .submenu_group {
    border-bottom: 1px solid #cccccc;
    margin:10px 0 0 0;
    padding-bottom: 10px;
}

#owner_block_submenu .submenu_group .submenu_group_filter ul li a,
#owner_block_submenu .submenu_group .submenu_group_filetypes ul li a {
    color:#666666;
}
#owner_block_submenu .submenu_group .submenu_group_filter ul li.selected a,
#owner_block_submenu .submenu_group .submenu_group_filetypes ul li.selected a {
    background:#999999;
    color:white;
}
#owner_block_submenu .submenu_group .submenu_group_filter ul li a:hover,
#owner_block_submenu .submenu_group .submenu_group_filetypes ul li a:hover {
    color:white;
    background: #999999;
}

#add-box h2
{
    color:#0054A7;
    font-size:1.35em;
    line-height:1.2em;
    margin:0pt 0pt 5px;
}

.admin_users_online .profile_status {
    -webkit-border-radius: 4px; 
    -moz-border-radius: 4px;
    background:#bbdaf7;
    line-height:1.2em;
    padding:2px 4px;
}
.admin_users_online .profile_status span {
    font-size:90%;
    color:#666666;
}
.admin_users_online  p.owner_timestamp {
    padding-left:3px;
}

.admin_users_online .search_listing {
    margin:0 0 5px 0;
    padding:5px;
    border:2px solid #cccccc;
    -webkit-border-radius: 5px; 
    -moz-border-radius: 5px;
}


.admin_debug label,
.admin_usage label {
    color:#333333;
    font-size:100%;
    font-weight:normal;
}

.admin_usage {
    border-bottom:1px solid #cccccc;
    padding:0 0 20px 0;
}

.usersettings_statistics td,
.admin_statistics td {
    padding:2px 4px 2px 4px;
    border-bottom:1px solid #cccccc;
}
.usersettings_statistics td.column_one,
.admin_statistics td.column_one {
    width:200px;
}
.usersettings_statistics table,
.admin_statistics table {
    width:100%;
}
.usersettings_statistics table,
.admin_statistics table {
    border-top:1px solid #cccccc;
}
.usersettings_statistics table tr:hover,
.admin_statistics table tr:hover {
    background: #E4E4E4;
}

#logbrowserSearchform {
    padding: 10px;
    background-color: #dedede;
    -webkit-border-radius: 8px; 
    -moz-border-radius: 8px;
}

.log_entry {
    width: 699px;
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