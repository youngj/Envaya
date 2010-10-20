<div class="heading_container">
<?php 
    echo @$vars['area3']; 
    echo SessionMessages::view_all();
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
<?php echo view('page_elements/content_footer'); ?>
</div>
</div>
