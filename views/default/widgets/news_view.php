<div class='section_content'>
<?php 
    $widget = $vars['widget'];
    $org = $widget->getContainerEntity();

    $offset = (int) get_input('offset');
    $limit = 10;

    $count = $org->queryNewsUpdates()->count();
    $entities = $org->queryNewsUpdates()->limit($limit, $offset)->filter();            
?>

<?php if (!empty($entities)) { ?>
<div class='view_toggle'>
    <strong><?php echo __('list') ?></strong> | <a href='<?php echo rewrite_to_current_domain($entities[0]->getURL()) ?>'><?php echo __('blog:timeline') ?></a>
</div>
<div style='clear:both'></div>
<?php } ?>
<?php

    echo view_entity_list($entities, $count, $offset, $limit);
    
    if (!$count)
    {
        echo "<div class='padded'>".__("widget:news:empty")."</div>";
    }

?>
</div>