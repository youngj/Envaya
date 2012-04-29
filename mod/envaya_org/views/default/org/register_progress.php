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
        $class = (($current == $step) ? 'active' : '');
        $style = ($step < 3) ? 'border-right:1px solid #ddd' : '';
        echo "<td style='$style' class='tab $class'>";
        echo "<span>$text</span>";
        echo "</td>";
    }   
?>
</tr>    
</table>