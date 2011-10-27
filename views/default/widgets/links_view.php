<div class='section_content padded'>
<?php 
    $widget = $vars['widget'];
    $org = $widget->get_container_user();    
    $query = $org->query_external_sites();    
    $sites = $query->filter();
    
    foreach ($sites as $site)
    {
        echo view('widgets/links_view_site', array('site' => $site));
    }
?>
</div>