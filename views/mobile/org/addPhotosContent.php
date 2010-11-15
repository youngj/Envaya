<div class='input'>
<?php echo view('input/hidden', array('internalname' => 'imageNumber', 'value' => '1')); ?>
<?php echo view('input/hidden', array('internalname' => 'sizes', 'value' => json_encode(Widget::get_image_sizes()))); ?>

<label><?php echo __('photo:file'); ?></label><br />
<?php echo view('input/file', array('internalname' => 'imageFile1')); ?>
</div>
<div class='input'>
<label><?php echo __('photo:caption'); ?></label><br />
<?php echo view('input/longtext', array('internalname' => 'imageCaption1', 'trackDirty' => true)) ?>
</div>
<div>
<?php echo view('input/submit', array('value' => __('publish'), 'trackDirty' => true)) ?>
</div>
</div>