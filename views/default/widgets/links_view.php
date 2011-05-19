<div class='section_content padded'>
<?php 
    $widget = $vars['widget'];
    $org = $widget->get_root_container_entity();
    
    $query = $org->query_external_sites();
    
    $sites = $query->filter();
    
    if ($sites)
    {
        foreach ($sites as $site)
        {
            echo view_entity($site);
        }
    }
?>
</div>