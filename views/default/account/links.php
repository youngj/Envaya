<?php
    $user = $vars['user'];        
?>
<table style='width:100%'>
<tr>
<td>
    <?php echo view('account/links_items', array('user' => $user)); ?>
    <a class='icon_link icon_logout' href='/pg/logout'><?php echo __('logout') ?></a>    
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
