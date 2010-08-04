<div class="heading_container">
<?php 
    echo @$vars['area3']; 
    echo view('messages/list', array('object' => system_messages(null,"")));
    echo $vars['area1'];
?>
</div>
<div class="content_container">
    <div class="thin_column">    
        <?php echo $vars['area2']; ?>
    </div>
</div>
<div class="footer_container">
<div class='thin_column'>
<?php
    echo get_submenu_group('footer', 'canvas_header/link_submenu', 'canvas_header/footer_submenu_group'); 
?>
<div class='language'>        
    <?php echo get_language_links(); ?>    
</div>
</div>
</div>
