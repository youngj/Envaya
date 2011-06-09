<?php
        $testing_country = get_input('testing_country');

        if (!empty($testing_country) && !Geography::is_supported_country($testing_country))
        {
            throw new ValidationException(__("register:wrong_country"));
        }
?>
<?php echo view("org/register_progress", array('current' => 1)) ?>

<div class='padded'>
<div id='instructions'>
    <?php echo __('register:welcome') ?>
</div>
<div class='instructions'>
    <?php echo __('register:qualify_instructions') ?>
</div>

<form action='<?php echo secure_url('/org/register1'); ?>' method='POST'>

<?php echo view('input/securitytoken'); ?>

<div class='input'>
<label><?php echo __('register:org_type') ?></label><br />

<?php echo view('input/radio',
    array('name' => 'org_type',
        'options' => array(
            'np' => __('register:org_type:non_profit'),
            'p' => __('register:org_type:for_profit'),
            'other' => __('register:org_type:other'),
        ))
    ) ?>
</div>

<div class='input'>
<label><?php echo __('register:country') ?></label><br />

<?php
 $country_options= array();
 $country_options['tz'] = __('country:tz');
 if ($testing_country)
 {
	$country_options[$testing_country] = __('country:'.$testing_country);
 }
 $country_options['other'] = __('country:other');

 echo view('input/radio',
    array('name' => 'country',
        'options' => $country_options)
    ); 
 ?>
</div>

<div class='input'>
<label><?php echo __('register:click_to_continue') ?></label>
<br />
<?php echo view('input/submit',array(
    'value' => __('register:next_step')
));
?>
</div>


</form>

</div>
