<?php
    $filters = $vars['filters'];    
    $changeFilterUrl = $vars['changeurl'];
    
    $link_url = $changeFilterUrl;
    foreach ($filters as $filter)
    {
        $link_url = url_with_param($link_url, $filter->get_param_name(), $filter->value);        
    }
?>

<div class='padded' style='border-bottom:1px solid #ccc;'>
<?php
foreach ($filters as $filter)
{
    if ($filter->is_valid())
    {
        echo $filter->get_name().": ";
        echo "<a href='".escape($link_url)."'>";
        echo escape($filter->render_view());
        echo "</a>";
        echo "<br />";
    }
}
?>
</div>
