<div class='padded'>
<table class='gridTable'>
<?php
    $emails = $vars['emails'];
    
    foreach ($emails as $email)
    {
        ?>
        <tr>
            <td>
            <?php echo ($email->active) ? "<b>" : '' ?>
            <?php echo escape($email->subject ?: 'No Subject') ?>
            <?php echo ($email->active) ? "</b>" : '' ?>
            </td>            
            <td><?php echo __($email->get_language()) ?></td>            
            <td><a href='/admin/view_email?email=<?php echo $email->guid ?>'><?php echo __('view') ?></a></td>
            <td><a href='/admin/edit_email?email=<?php echo $email->guid ?>'><?php echo __('edit') ?></a></td>
            <td><a href='/admin/batch_email?email=<?php echo $email->guid ?>'><?php echo __('send') ?></a></td>
            <td>
            <?php if (!$email->active) { 
            
                echo view('output/confirmlink', array(
                    'text' => 'Activate',
                    'href' => "admin/activate_email?email={$email->guid}"
                ));
            
             } else { echo "Active"; }             
             
             ?>
            </td>
        </tr>
        <?php
    }
?>
</table>
<a href='/admin/add_email'>Create new email template</a>
</div>