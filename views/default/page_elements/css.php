<?php
    if (@$vars['css_url'])
    {
        echo "<link rel='stylesheet' href='".escape($vars['css_url'])."' type='text/css' />";
    }
?>
<!--[if IE 6]>
<style type='text/css'>
#top_menu_container a {width:10px}
.loggedInAreaContent {padding-bottom:0px}
.home_about, .home_content {background-image:none}
</style>
<![endif]-->