
<div class="heading_container">
<?php 
    echo @$vars['area3']; 
    echo elgg_view('messages/list', array('object' => system_messages(null,"")));
    echo $vars['area1'];
?>
</div>
<div class="content_container">
<div class="thin_column">    
    <?php            
        $submenu = get_submenu_group('topnav', 'canvas_header/link_submenu', 'canvas_header/basic_submenu_group'); 
        if (!empty($submenu))
        {
            echo "<div id='site_menu'>$submenu</div>";
        }    
        else
        {
            echo "<div id='no_site_menu'></div>";
        }
    ?>    

    <div id='content'>
        <div id='content_top'></div>
        <div id='content_mid'>       
            <?php echo elgg_view('translation/control_bar'); ?>
            <?php echo $vars['area2']; ?>
            &nbsp;    
            <div style='clear:both'></div>        
        </div>
        

        <div id='content_bottom'></div>        
    </div>
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
