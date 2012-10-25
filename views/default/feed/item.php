<?php
    $feedItem = $vars['item'];
    $user = $feedItem->get_user_entity();
    $mode = @$vars['mode'];
    
    $show_edit_controls = @$vars['show_edit_controls'];        
?>
<div class="feed_post padded">
<?php                 
    if ($mode != 'self') 
    {        
        echo view('feed/icon', array('user' => $user));
        echo "<div class='feed_content'>";
    } 
    echo $feedItem->render_thumbnail($mode);
    echo "<div class='feed_heading'>";
    echo $feedItem->render_heading($mode);
    echo "</div>";
    echo $feedItem->render_content($mode);
    ?>               
    <div class='blog_date'><?php echo $feedItem->get_date_text() ?></div>
    <?php
    
    $user = $feedItem->get_user_entity();
    
    if ($show_edit_controls) 
    {
        $edit_links = [];
        if (Permission_EditFeaturedItems::has_for_entity($user))
        {            
            $edit_links[] = view('input/post_link', array(
                'href' => "/pg/feature_feed_item?item={$feedItem->id}&featured=" . ($feedItem->featured ? 0 : 1),
                'text' => $feedItem->featured ? 'Unfeature' : 'Feature',
                'class' => 'admin_links',
                //'confirm' => __('feed:confirm_delete'),
            ));
        }
    
        if (Permission_EditUserSite::has_for_entity($user) || Permission_UseAdminTools::has_for_entity($user))
        {    
            $edit_links[] =  view('input/post_link', array(
                'href' => "/pg/delete_feed_item?item={$feedItem->id}",
                'text' => __('delete'),
                'class' => 'admin_links',
                'confirm' => __('feed:confirm_delete'),
            ));
        }               
        
        echo implode(' &middot; ', $edit_links);        
   }
   
    if ($mode != 'self') 
    {        
        echo "</div>";
    }          
    ?>
    <div style='clear:both'></div>
</div>
