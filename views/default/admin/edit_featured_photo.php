<?php
    $photo = $vars['photo'];
?>
<form method='POST' action='/admin/save_featured_photo'>
<?php 

    echo view('input/securitytoken');

    echo view('input/hidden', array(
        'name' => 'guid',
        'value' => $photo->guid
    ));

    echo view('input/hidden', array(
        'name' => 'x_offset',
        'id' => 'x_offset',
        'value' => $photo->x_offset
    ));
    
    echo view('input/hidden', array(
        'name' => 'y_offset',
        'id' => 'y_offset',
        'value' => $photo->y_offset
    ));    
 ?>

<?php 
echo view('admin/preview_featured_photo', array(
    'image_url' => $photo->image_url,
    'x_offset' => $photo->x_offset,
    'y_offset' => $photo->y_offset,
    'id' => 'photo'
));

echo view('admin/nudge_photo', array(
    'x_offset_id' => 'x_offset',
    'y_offset_id' => 'y_offset',
    'photo_id' => 'photo'
));

?>

<div class='input'>
<label>Organization Name</label>
<?php echo view('input/text', array('name' => 'org_name', 'value' => $photo->org_name)); ?>
</div>

<div class='input'>
<label>Caption</label><br />
<?php echo view('input/text', array('name' => 'caption', 'value' => $photo->caption)); ?>
</div>

<div class='input'>
<label>Link</label><br />
<?php echo view('input/text', array('name' => 'href', 'value' => $photo->href)); ?>
</div>

<div class='input'>
<label>Weight</label><br />
<?php echo view('input/text', array('name' => 'weight', 'value' => $photo->weight)); ?>
</div>

<div class='input'>
<label>Active?</label><br />
<?php echo view('input/radio', array('name' => 'active',
    'options' => yes_no_options(), 'value' => $photo->active ? 'yes' : 'no')); ?>
</div>

<?php

    echo view('input/submit', array('value' => __('publish')));

    
?>

</form>