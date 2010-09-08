<?php 
    ob_start();
?>

<?php            
    $submenu = get_submenu_group('topnav', 'canvas_header/link_submenu', 'canvas_header/basic_submenu_group'); 
    if (!empty($submenu))
    {
        echo "<div id='site_menu'>$submenu<div style='clear:both'></div></div>";
    }    
    else
    {
        echo "<div id='no_site_menu'></div>";
    }
?>    

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
    echo view_layout("content_shell", $vars['area1'], $content, @$vars['area3']);    
?>
