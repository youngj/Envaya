<?php 
    ob_start();
?>
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
        <?php echo $vars['area2']; ?>            
        <div style='clear:both'></div>        
    </td>
    </tr>
</table>
</div>
<div id='content_bottom'>
</div>
<script type='text/javascript'>addImageLinks(document.getElementById('right_content'));</script>

<?php 
    $content = ob_get_clean();
    echo view("canvas/layouts/content_shell", array(
        'area1' => $vars['area1'],
        'area2' => $content,
        'area3' => @$vars['area3']
    ));
?>
