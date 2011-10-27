<div class='section_content padded'>
<?php
    $entity = $vars['entity'];
    
    $cls = get_class($entity);
    $view_name = $cls::$admin_view;
    if ($view_name)
    {
        echo view($view_name, array('entity' => $entity));
    }
    
    $url = $entity->get_url();
    
    if ($url)
    {   
        $esc_url = escape($url);
        echo "<br /><a href='$esc_url'>$esc_url</a>";
    }
    echo "<br />";
        
    echo "Created: ".friendly_time($entity->time_created)."<br />";
    
    if ($entity->time_updated > $entity->time_created)
    {
        echo "Updated: ".friendly_time($entity->time_updated)."<br />";
    }
    
    $owner = $entity->get_owner_entity();
    
    if ($owner)
    {
        echo "Owner: <a href='{$owner->get_admin_url()}'>".escape($owner->get_title())."</a>";
    }
    echo "Type: ".get_class($entity)."<br />";
    
    echo "<div class='admin_links'>";
    if ($entity->is_enabled())
    {
        echo view('input/post_link', array(
            'text' => __('entity:disable'),
            'confirm' => __('areyousure'),        
            'href' => "{$entity->get_admin_url()}/disable"
        ));        
    }
    else
    {
        echo view('input/post_link', array(
            'text' => __('entity:enable'),
            'confirm' => __('areyousure'),        
            'href' => "{$entity->get_admin_url()}/enable"
        ));            
    }
    echo "</div>";
?>
</div>