<?php
    $template = $vars['template'];
    $template_class = get_class($template);
?>
<div style='padding-bottom:10px'>
<strong>Filters:</strong> (<?php echo $template->query_filtered_subscriptions()->count(); ?>/<?php 
echo $template_class::query_all_subscriptions()->count(); ?></span> recipients in filter)
<?php
    foreach ($template->get_filters() as $filter)
    {
        echo "<div style='padding-left:60px'><strong>{$filter->get_name()}</strong>: {$filter->render_view()}</div>";
    }
?>
</div>