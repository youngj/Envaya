<?php

$featured_site = $vars['featured_site'];
$org = $featured_site->get_container_entity();
$show_date = isset($vars['show_date']) ? $vars['show_date'] : true;

if ($org) 
{
?>
<div class='featured_site'>
<div class='featured_site_name'><a href='<?php echo $org->get_url() ?>'><?php echo escape($org->name) ?></a></div>
<?php 
if ($featured_site->image_url)
{
    echo "<a href='{$org->get_url()}'><img src='".escape($featured_site->image_url)."' class='image_left' /></a>";
}
echo $featured_site->render_content(); ?>

<?php 
if ($show_date)
{
?>

<div class='blog_date'><?php echo friendly_time($featured_site->time_created) ?></div>

<?php 
if (Permission_EditMainSite::has_for_root())
{
    ?>
    <span class='admin_links'>
    <?php 
    if (!$featured_site->active) 
    { 
        echo view('input/post_link', array(
                'text' => __('featured:activate'),
                'confirm' => __('areyousure'),
                'href' => "admin/envaya/activate_featured?guid={$featured_site->guid}"
            ));        
    } 
    else
    {
        echo escape(__('featured:active'));
    }    
        
    ?>              
    
    <a href='/admin/envaya/edit_featured?guid=<?php echo $featured_site->guid ?>'><?php echo escape(__('edit')) ?></a>
    <?php
    echo view('input/post_link', array(
        'text' => __('delete'),
        'confirm' => __('areyousure'),        
        'href' => "{$featured_site->get_admin_url()}/disable"
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