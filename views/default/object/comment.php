<?php
    $entity = $vars['entity'];
?>

<div class="comment">
<?php 
if ($entity->is_enabled())
{
?>
    <div class='comment_name'>
    <?php
        $owner = $entity->get_owner_entity();
        
        if ($owner)
        {    
            $nameHTML = escape($owner->name);
            if ($owner instanceof Organization)
            {
                $nameHTML = "<a href='{$owner->get_url()}'>$nameHTML</a>";
            }
        }
        else
        {
            $nameHTML = escape($entity->get_name());        
        }
        
        if ($entity->location)
        {
            $nameHTML = "$nameHTML (".escape($entity->location).")";
        }
        echo sprintf(__('comment:name_said'), $nameHTML);
    ?>
    </div>
    <?php    
		echo nl2br(escape($entity->content));
    ?>  
    <div class='blog_date'><?php echo $entity->get_date_text(); ?></div>
    <?php
        if ($entity->can_edit()) 
        {
            echo "<span class='admin_links'>";
            echo view('output/confirmlink', array(
                'href' => "/pg/delete_comment?comment={$entity->guid}",
                'text' => __('delete'),
                'confirm' => __('comment:confirm_delete')
            ));
            echo "</span>";
        }    
    ?>
<?php
}
else
{
	echo "<div class='comment_deleted'>[".__('comment:deleted_marker')."]</div>";
}
?>
</div>