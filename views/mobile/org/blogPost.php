<div class='section_content'>
<?php

    $entity = $vars['entity'];              
    $url = rewrite_to_current_domain($entity->get_url());
    $org = $entity->get_root_container_entity();
    $blogDates = $org->get_blog_dates();
?>

<div class='view_toggle'>
    <a href='<?php echo rewrite_to_current_domain($org->get_url().'/news') ?>'><?php echo __('list') ?></a> | <strong><?php echo __('blog:timeline') ?></strong>
</div>
<div style='clear:both'></div>

<?php

if (sizeof($blogDates) > 1)
{

?>
<div style='text-align:center'>
<a href='<?php echo "$url/prev"; ?>'>&lt; <?php echo __('previous'); ?></a>
<a href='<?php echo "$url/next"; ?>'><?php echo __('next'); ?> &gt;</a>
</div>
<?php
}
            
echo view_entity($entity, true);
?>

</div>
</div>