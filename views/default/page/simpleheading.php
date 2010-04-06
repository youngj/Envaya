<?php 
$page = $vars['page'];

$class = @$vars['org_only'] ? "org_only_heading" : "";
    
?>

<div class="simple_heading <?php echo $class ?>">
<?php echo escape($vars['title']) ?>
</div>