<?php 
    ob_start();
?>

<?php echo view('page_elements/site_menu'); ?>
<div id='content'>
    <div id='content_top'></div>
    <div id='content_mid'>       
        <?php echo view('translation/control_bar'); ?>
        <?php echo $vars['area2']; ?>            
        <div style='clear:both'></div>        
    </div>        
    <div id='content_bottom'></div>        
</div>
<script type='text/javascript'>addImageLinks(document.getElementById('content_mid'));</script>

<?php 
    $content = ob_get_clean();
    
    echo view("canvas/layouts/content_shell", array(
        'area1' => $vars['area1'],
        'area2' => $content,
        'area3' => @$vars['area3']
    ));        
?>
