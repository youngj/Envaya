<?php
    $entity = $vars['entity'];
?>

<div class="comment">
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
            $nameHTML = escape($entity->name ?: __('comment:anonymous'));        
        }
    
        echo sprintf(__('comment:name_said'), $nameHTML);
    ?>
    </div>
    <?php    
        echo $entity->render_content();
    ?>  
    <div class='blog_date'><?php echo $entity->get_date_text(); ?></div>
    <?php
        if ($entity->can_edit()) 
        {
            echo "<span class='admin_links'>";
            echo view('output/confirmlink', array(
                'is_action' => true,
                'href' => "/pg/delete_comment?comment={$entity->guid}",
                'text' => __('delete')
            ));
            echo "</span>";
        }    
    ?>
</div>