<?php
    $photo = $vars['photo'];
?>
<form method='POST' action='/admin/save_featured_photo'>
<?php 

    echo view('input/securitytoken');

    echo view('input/hidden', array(
        'internalname' => 'guid',
        'value' => $photo->guid
    ));

    echo view('input/hidden', array(
        'internalname' => 'x_offset',
        'internalid' => 'x_offset',
        'value' => $photo->x_offset
    ));
    
    echo view('input/hidden', array(
        'internalname' => 'y_offset',
        'internalid' => 'y_offset',
        'value' => $photo->y_offset
    ));    
 ?>

<?php 
echo view('admin/preview_featured_photo', array(
    'image_url' => $photo->image_url,
    'x_offset' => $photo->x_offset,
    'y_offset' => $photo->y_offset,
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
<?php echo view('input/text', array('internalname' => 'org_name', 'value' => $photo->org_name)); ?>
</div>

<div class='input'>
<label>Caption</label><br />
<?php echo view('input/text', array('internalname' => 'caption', 'value' => $photo->caption)); ?>
</div>

<div class='input'>
<label>Link</label><br />
<?php echo view('input/text', array('internalname' => 'href', 'value' => $photo->href)); ?>
</div>

<div class='input'>
<label>Weight</label><br />
<?php echo view('input/text', array('internalname' => 'weight', 'value' => $photo->weight)); ?>
</div>

<div class='input'>
<label>Active?</label><br />
<?php echo view('input/radio', array('internalname' => 'active',
    'options' => yes_no_options(), 'value' => $photo->active ? 'yes' : 'no')); ?>
</div>

<?php

    echo view('input/submit',
        array('internalname' => 'submit',
            'class' => "submit_button",
            'trackDirty' => true,
            'value' => __('publish')));

    
?>

</form>