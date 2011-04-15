<?php 
    ob_start();
?>

<div class='thin_column'>
<?php echo view('page_elements/site_menu', $vars); ?>
<div id='content'>
    <div id='content_top'></div>
    <div id='content_mid'>       
        <?php echo view('translation/control_bar'); ?>
        <?php echo $vars['content']; ?>            
        <div style='clear:both'></div>        
    </div>        
    <div id='content_bottom'></div>        
</div>
</div>
<script type='text/javascript'>addImageLinks(document.getElementById('content_mid'));</script>

<?php 
    $vars['content'] = ob_get_clean();        
    $vars['header'] = "<div class='thin_column'>{$vars['header']}</div>";    
    
    echo view("layouts/content_shell", $vars);        
?>