<?php 
    ob_start();
?>
<div class='thin_column'>
<div id='content_wrapper'>
<table class='left_sidebar_table'>
<tr>
<td id='left_sidebar'>
<?php echo view('page_elements/site_menu', $vars); ?>
</td>
<td id='right_content'>
<?php 
    echo view('page_elements/translate_bar', $vars); 
    echo $vars['content']; 
?>         
<div style='clear:both'></div>
</td>
</tr>
</table>
</div>
<div id='content_bottom'>
</div>
</div>
<?php 
    echo view('page_elements/add_image_links', array('id' => 'right_content'));

    $vars['content'] = ob_get_clean();
    $vars['header'] = "<div class='thin_column'>{$vars['header']}</div>";    
    
    echo view("layouts/content_shell", $vars);
?>
