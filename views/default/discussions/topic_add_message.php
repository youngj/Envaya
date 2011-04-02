<?php
    $topic = $vars['topic'];
    $org = $topic->get_container_entity();
    $user = Session::get_loggedin_user();  
    
    ob_start();
?>
<form method='POST' action='<?php echo $topic->get_url(); ?>/add_message'>

<?php 
    echo view('input/tinymce', array('name' => 'content', 'autoFocus' => true));    
?>

<table class='inputTable'>
<tr>
<th><?php echo __('discussions:name'); ?></th>
<td>
<?php 
    $name = Session::get('user_name');
    echo view('input/text', array('name' => 'name', 'value' => $name)); 
?>
</td>
</tr></table>

<div style='float:right'>
<br />
<br />
<a href='<?php echo $topic->get_url(); ?>'><?php echo __('discussions:back_to_messages'); ?></a>
</div>

<?php
    echo view('input/securitytoken');
    echo view('input/submit', array('value' => __('discussions:publish_message')));    
 ?>
</form>

<?php
    $content = ob_get_clean();
    
    
    echo view('section', array(
        'header' => escape($topic->subject), 
        'content' => $content
    ));
    
?>