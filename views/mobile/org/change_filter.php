<?php

$filters = $vars['filters'];    
$baseurl = $vars['baseurl'];

?>

<form method='GET' action='<?php echo $baseurl; ?>'>
<?php
foreach ($filters as $filter)
{
    if ($filter->is_valid())
    {    
        echo "<div>";
        echo $filter->render_input(array(
            'name' => $filter->get_param_name(),
        ));
        echo "</div>";
    }
}
    
echo view('input/submit', array('value' => __('go'))); 
?>
</div>
</form>
