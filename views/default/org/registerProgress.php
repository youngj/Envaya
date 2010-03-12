<?php 
    $current = $vars['current'];
?>    
<ul class='progressTabs'>
<?php
    $steps = array(1,2,3);
    foreach ($steps as $step)
    {
        ?>
        <li class='<?php echo (($current == $step) ? 'active' : '') ?>'>
            <?php echo elgg_echo("register:step".$step) ?>
        </li>
        <?php
    }   
    ?>
</ul>
<div style='clear:both;height:10px;'></div>