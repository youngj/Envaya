<?php

$entity = $vars['entity'];
$org = $entity->get_container_entity();

if ($org) 
{
?>
<div class='featured_site'>
<div class='featured_site_name'><a href='<?php echo $org->get_url() ?>'><?php echo escape($org->name) ?></a></div>
<?php 
if ($entity->image_url)
{
    echo "<a href='{$org->get_url()}'><img src='".escape($entity->image_url)."' class='image_left' /></a>";
}
echo $entity->render_content(); ?>

<?php if ($vars['full']) { ?>
    <div class='blog_date'><?php echo friendly_time($entity->time_created) ?></div>
    <?php 
    if (Session::isadminloggedin())
    {
        ?>
        <span class='admin_links'>
        <?php 
        if (!$entity->active) 
        { 
            echo view('output/confirmlink', array(
                    'text' => __('featured:activate'),
                    'is_action' => true,
                    'href' => "admin/activate_featured?guid={$entity->guid}"
                ));        
        } 
        else
        {
            echo escape(__('featured:active'));
        }    
        
        if (get_language() != $entity->get_language())
        {
            $escUrl = urlencode($_SERVER['REQUEST_URI']);                   
            echo " <a href='/org/translate?from=$escUrl&prop[]={$entity->guid}.content.1'>".__("trans:contribute")."</a>";
        }
        
        ?>              
        
        <a href='/admin/edit_featured?guid=<?php echo $entity->guid ?>'><?php echo escape(__('edit')) ?></a>
        <?php
        echo view('output/confirmlink', array(
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