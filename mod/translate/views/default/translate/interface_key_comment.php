<?php
    $entity = $vars['comment'];
?>

<div class="comment">
<?php 
echo $entity->render_content();
?>  
<div class='blog_date'><?php          
    echo strtr(__('date:date_name'), array(
        '{date}' => $entity->get_date_text(),
        '{name}' => escape($entity->get_owner_name())
    ));    
?></div>
<?php
    if ($entity->can_edit()) 
    {
        echo "<span class='admin_links'>";
        echo view('output/confirmlink', array(
            'href' => "/tr/delete_comment?comment={$entity->guid}",
            'text' => __('delete'),
            'confirm' => __('comment:confirm_delete')
        ));
        echo "</span>";
    }    
?>
</div>