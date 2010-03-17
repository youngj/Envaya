<?php 
    $current = $vars['current'];
?>    
<table class='tabs'>
<tr>
<?php
    $steps = array(1,2,3);
    foreach ($steps as $step)
    {
        ?>
        <td class='tab <?php echo (($current == $step) ? 'active' : '') ?>'>
            <span><?php echo elgg_echo("register:step".$step) ?></span>
        </td>
        <?php
    }   
    ?>
</tr>    
</table>