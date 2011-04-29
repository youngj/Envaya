<div class="heading_container">
<?php 
    echo @$vars['pre_body']; 
    echo SessionMessages::view_all();    
    echo $vars['header'];        
?>
</div>
<div class="content_container">
<?php echo $vars['content']; ?>    
</div>
<div class="footer_container">
<div class='thin_column'>
<?php echo view('page_elements/content_footer', $vars); ?>
</div>
</div>