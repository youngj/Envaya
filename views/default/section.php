<?php
    if (@$vars['header'])
    {
?>
<div class='section_header'>
    <?php echo $vars['header']; ?>  
</div>
<?php 
    }
?>
<div class='section_content padded'>
    <?php echo $vars['content'] ?>  
    <div style='clear:both'></div>
</div>