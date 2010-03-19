<?php 

echo $vars['area3']; 

?>

<div id="thin_column">
    <div id='heading'>
      <?php echo $vars['area1'] ?>  
    </div>

    <?php            
        $submenu = get_submenu_group('a', 'canvas_header/topbar_submenu', 'canvas_header/topbar_submenu_group'); 
        if (!empty($submenu))
        {
            echo "<div id='site_menu'>$submenu</div>";
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

