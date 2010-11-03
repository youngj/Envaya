<div class='section_content'>
<?php

    $entity = $vars['entity'];              
    $url = rewrite_to_current_domain($entity->get_url());
    $org = $entity->get_root_container_entity();
    $blogDates = $org->get_blog_dates();
        
    echo view_entity($entity, array('single_post' => true));
    
    if (sizeof($blogDates) > 1)
{

?>
<div style='text-align:center'>
<a href='<?php echo "$url/prev"; ?>'>&lt; <?php echo __('previous'); ?></a>
<a href='<?php echo "$url/next"; ?>'><?php echo __('next'); ?> &gt;</a>
</div>
<?php
}
?>


</div>
</div>