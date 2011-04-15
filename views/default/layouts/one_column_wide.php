<?php 
    ob_start();
?>
<div class='wide_column'>
<div id='content'>
    <?php echo $vars['content']; ?>            
    <div style='clear:both'></div>        
</div>        
</div>
<?php 
    $vars['content'] = ob_get_clean();    
    echo view("layouts/content_shell", $vars);        
?>