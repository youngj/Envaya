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
    
    if ($show_edit_controls && $feedItem->can_edit()) 
    {            
        echo view('input/post_link', array(
            'href' => "/pg/delete_feed_item?item={$feedItem->id}",
            'text' => __('delete'),
            'class' => 'admin_links',
            'confirm' => __('feed:confirm_delete'),
        ));            
   }            
   
    if ($mode != 'self') 
    {        
        echo "</div>";
    }          
    ?>
    <div style='clear:both'></div>
</div>
