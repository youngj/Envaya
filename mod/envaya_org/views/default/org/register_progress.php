<?php 
    $current = $vars['current'];
    
    $steps = array(
        1 => __('register:step1'),
        2 => __('register:step2'),
        3 => __('register:step3'),
    );
?>    
<table class='tabs'>
<tr>
<?php
    foreach ($steps as $step => $text)
    {
        ?>
        <td class='tab <?php echo (($current == $step) ? 'active' : '') ?>'>
            <span><?php echo $text; ?></span>
        </td>
        <?php
    }   
    ?>
</tr>    
</table>