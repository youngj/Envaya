<?php

$entity = $vars['entity'];
$org = $entity->getContainerEntity();

if ($org) 
{
?>
<div class='featured_site'>
<strong><a href='<?php echo $org->getURL() ?>'><?php echo escape($org->name) ?></a></strong><br />
<?php 
echo $entity->renderContent(); ?>

<?php if ($vars['full']) { ?>
    <div class='blog_date'><?php echo friendly_time($entity->time_created) ?></div>
    <?php 
    if (isadminloggedin())
    {
        ?>
        <span class='admin_links'>
        <?php 
        if (!$entity->active) 
        { 
            echo elgg_view('output/confirmlink', array(
                    'text' => __('featured:activate'),
                    'is_action' => true,
                    'href' => "admin/activate_featured?guid={$entity->guid}"
                ));        
        } 
        else
        {
            echo escape(__('featured:active'));
        }    
        ?>
        <a href='admin/edit_featured?guid=<?php echo $entity->guid ?>'><?php echo escape(__('edit')) ?></a>
        <?php
        echo elgg_view('output/confirmlink', array(
            'text' => __('delete'),
            'is_action' => true,
            'href' => "admin/delete_entity?guid={$entity->guid}"
        ));        
        ?>
        </span>
        <?php
    }
}
    ?>

</div>

<?php
}

?>