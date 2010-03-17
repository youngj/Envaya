<?php echo elgg_view("org/registerProgress", array('current' => 1)) ?>

<div class='padded'>
<div id='instructions'>
    <?php echo elgg_echo('qualify:welcome') ?>
</div>
<div class='instructions'>
    <?php echo elgg_echo('qualify:instructions') ?>
</div>

<form action='action/org/register1' method='POST'>

<div class='input'>
<label><?php echo elgg_echo('qualify:org_type') ?></label><br />

<?php echo elgg_view('input/radio', 
    array('internalname' => 'org_type', 
        'options' => array(
            'np' => elgg_echo('qualify:org_type:non_profit'),
            'p' => elgg_echo('qualify:org_type:for_profit'),
            'other' => elgg_echo('qualify:org_type:other'),
        ))
    ) ?>
</div>

<div class='input'>
<label><?php echo elgg_echo('qualify:country') ?></label><br />

<?php echo elgg_view('input/radio', 
    array('internalname' => 'country', 
        'options' => array(
            'tz' => elgg_echo('country:tz'),
            'other' => elgg_echo('country:other'),
        ))
    ) ?>
</div>


<div class='input'>
<label><?php echo elgg_echo('qualify:org_info') ?></label><br />

<?php echo elgg_view('input/checkboxes', 
    array('internalname' => 'org_info', 
        'options' => array(
            'citizen' => elgg_echo('qualify:org_info:citizen'),
            'local' => elgg_echo('qualify:org_info:local'),
            'funder' => elgg_echo('qualify:org_info:funder'),
        ))
    ) ?>
</div>

<!--
<div class='input'>
<label><?php echo elgg_echo('qualify:registration_number') ?></label>
<?php echo elgg_view('input/text', 
    array('internalname' => 'registration_number', 'js' => 'style="width:150px"')) ?>
<div class='help'><?php echo elgg_echo('qualify:registration_number:help') ?></div>
</div>
-->

<div class='input'>
<label><?php echo elgg_echo('qualify:next') ?></label>
<br />
<?php echo elgg_view('input/submit',array(
    'value' => elgg_echo('qualify:next:button')
));
?>
</div>


</form>

</div>
