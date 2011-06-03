<?php

$entity = $vars['entity'];
$org = $entity->get_container_entity();
$show_date = isset($vars['show_date']) ? $vars['show_date'] : true;

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

<?php 
if ($show_date)
{
?>

<div class='blog_date'><?php echo friendly_time($entity->time_created) ?></div>

<?php 
if (Session::isadminloggedin())
{
    ?>
    <span class='admin_links'>
    <?php 
    if (!$entity->active) 
    { 
        echo view('input/post_link', array(
                'text' => __('featured:activate'),
                'confirm' => __('areyousure'),
                'href' => "admin/envaya/activate_featured?guid={$entity->guid}"
            ));        
    } 
    else
    {
        echo escape(__('featured:active'));
    }    
    
    if (Language::get_current_code() != $entity->get_language())
    {
        $trans = new Translation();
        $trans->container_guid = $entity->guid;
        $trans->property = 'content';
        $trans->html = true;
    
        echo ' ' . view('translation/translate_link', array(
            'translations' => array($trans)
        )) . ' ';
    }
    
    ?>              
    
    <a href='/admin/envaya/edit_featured?guid=<?php echo $entity->guid ?>'><?php echo escape(__('edit')) ?></a>
    <?php
    echo view('input/post_link', array(
        'text' => __('delete'),
        'confirm' => __('areyousure'),        
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