<?php
    $user = $vars['user'];        
?>
<table style='width:100%'>
<tr>
<td>
    <?php 
        $links = PageContext::get_submenu('dashboard_links')->get_items();
        
        echo implode("<div class='icon_separator'></div>", $links);
        
        echo "<div class='icon_separator'></div>";
        
        echo view('input/post_link', array(
            'href' => '/pg/logout',
            'text' => __('logout'),
            'class' => 'icon_link icon_logout',
        ));
    ?>
</td>
<td>
    <a class='icon_link icon_explore' href='/pg/browse'><?php echo __("browse:title") ?></a>
    <div class='icon_separator'></div>
    <a class='icon_link icon_search' href='/pg/search'><?php echo __("search:title") ?></a>
    <div class='icon_separator'></div>
    <a class='icon_link icon_feed' href='/pg/feed'><?php echo __("feed:title") ?></a>
    <div class='icon_separator'></div>
    <a class='icon_link icon_help' href='/envaya/page/help'><?php echo __('help') ?></a>
</td>
</tr>
</table>
