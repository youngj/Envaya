<?php echo view("org/registerProgress", array('current' => 1)) ?>

<div class='padded'>
<div id='instructions'>
    <?php echo __('qualify:welcome') ?>
</div>
<div class='instructions'>
    <?php echo __('qualify:instructions') ?>
</div>

<form action='<?php echo Config::get('secure_url'); ?>org/register1' method='POST'>

<div class='input'>
<label><?php echo __('qualify:org_type') ?></label><br />

<?php echo view('input/radio',
    array('name' => 'org_type',
        'options' => array(
            'np' => __('qualify:org_type:non_profit'),
            'p' => __('qualify:org_type:for_profit'),
            'other' => __('qualify:org_type:other'),
        ))
    ) ?>
</div>

<div class='input'>
<label><?php echo __('qualify:country') ?></label><br />

<?php echo view('input/radio',
    array('name' => 'country',
        'options' => array(
            'tz' => __('country:tz'),
            'other' => __('country:other'),
        ))
    ) ?>
</div>

<div class='input'>
<label><?php echo __('qualify:next') ?></label>
<br />
<?php echo view('input/submit',array(
    'value' => __('qualify:next:button')
));
?>
</div>


</form>

</div>
