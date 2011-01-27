<?php
    $user = get_user($vars['user_guid']);
?>
<form method='POST' action='/admin/new_featured_photo'>
<?php 

    echo view('input/securitytoken');

    echo view('input/hidden', array(
        'internalname' => 'user_guid',
        'value' => $vars['user_guid']
    ));

    echo view('input/hidden', array(
        'internalname' => 'image_url',
        'value' => $vars['image_url']
    ));
    
    echo view('input/hidden', array(
        'internalname' => 'x_offset',
        'internalid' => 'x_offset',
        'value' => 0
    ));
    
    echo view('input/hidden', array(
        'internalname' => 'y_offset',
        'internalid' => 'y_offset',
        'value' => 0
    ));    
    

 ?>

<?php 
echo view('admin/preview_featured_photo', array(
    'image_url' => $vars['image_url'],
    'x_offset' => 0,
    'y_offset' => 0,
    'internalid' => 'photo'
));

echo view('admin/nudge_photo', array(
    'x_offset_id' => 'x_offset',
    'y_offset_id' => 'y_offset',
    'photo_id' => 'photo'
));

?>

<div class='input'>
<label>Organization Name</label>
<?php echo view('input/text', array('internalname' => 'org_name', 'value' => ($user ? $user->name : ""))); ?>
</div>

<div class='input'>
<label>Caption</label><br />
<?php echo view('input/text', array('internalname' => 'caption')); ?>
</div>

<div class='input'>
<label>Link</label><br />
<?php echo view('input/text', array('internalname' => 'href', 'value' => $vars['href'])); ?>
</div>

<div class='input'>
<label>Weight</label><br />
<?php echo view('input/text', array('internalname' => 'weight', 'value' => "0.0")); ?>
</div>

<div class='input'>
<label>Active?</label><br />
<?php echo view('input/radio', array('internalname' => 'active', 'options' => yes_no_options(), 'value' => 'yes')); ?>
</div>

<?php

    echo view('input/submit',
        array('internalname' => 'submit',
            'class' => "submit_button",
            'trackDirty' => true,
            'value' => __('publish')));

    
?>

</form>