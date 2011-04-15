<?php 
    ob_start();
?>
<div class='thin_column'>
<div id='content_wrapper'>
<table class='left_sidebar_table'>
    <tr>
    <td id='left_sidebar'>
    <?php            
        echo PageContext::get_submenu()->render(); 
    ?>    
    </td>
    <td id='right_content'>
        <?php echo view('translation/control_bar'); ?>
        <?php echo $vars['content']; ?>            
        <div style='clear:both'></div>        
    </td>
    </tr>
</table>
</div>
<div id='content_bottom'>
</div>
</div>
<script type='text/javascript'>addImageLinks(document.getElementById('right_content'));</script>
<?php 
    $vars['content'] = ob_get_clean();
    $vars['header'] = "<div class='thin_column'>{$vars['header']}</div>";    
    
    echo view("layouts/content_shell", $vars);
?>
