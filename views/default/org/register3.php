<?php echo elgg_view("org/registerProgress", array('current' => 3   )) ?>

<?php 
    $org = get_loggedin_user();
?>

<div class='padded'>
<div id='instructions'>
    <?php echo elgg_echo('setup:instructions') ?>
</div>

<form action='action/org/register3' method='POST'>

<?php echo elgg_view('input/securitytoken'); ?>

<div class='input'>
<label><?php echo elgg_echo('setup:mission') ?></label>
<div class='help'>
<?php echo elgg_echo('setup:mission:help') ?>
</div>
<?php echo elgg_view('input/longtext', array(
    'internalname' => 'mission',
    'value' => $org->getWidgetByName('home')->content
)) ?>    
</div>


<div class='input'>
<label><?php echo elgg_echo('setup:language') ?></label><br />
<?php echo elgg_view('input/language', array(
    'internalname' => 'content_language',
    'value' => $org->language
)) ?> 
<div class='help'>
<?php echo elgg_echo('setup:language:help') ?>
</div>
</div>

<div class='input'>
    <label><?php echo elgg_echo("setup:sector"); ?><br /></label>
    <?php
        echo elgg_view("input/checkboxes",array(
            'internalname' => 'sector', 
            'options' => Organization::getSectorOptions(), 
            'value' => $org->getSectors()));
    ?>
    <?php echo elgg_echo('setup:sector:other_specify') ?> <?php echo elgg_view('input/text', array(
    'internalname' => 'sector_other',
    'js' => 'style="width:200px"'
)) ?>    
</div>


<div class='input'>
<label><?php echo elgg_echo('setup:location') ?></label>
<div>
<?php echo elgg_echo('setup:city') ?> <?php echo elgg_view('input/text', array(
    'internalname' => 'city',
    'js' => 'style="width:200px"',
    'value' => $org->city
)) ?>, <?php echo escape($org->getCountryText()); ?>
</div>
<div>
<?php echo elgg_echo('setup:region') ?> <?php echo elgg_view('input/pulldown', array(
    'internalname' => 'region',
    'options_values' => regions_in_country($org->country),
    'empty_option' => elgg_echo('setup:region:blank'),
    'value' => $org->region
)) ?>    
</div>

</div>

<div class='input'>
<label><?php echo elgg_echo('setup:next') ?></label>
<div class='help'><?php echo elgg_echo('setup:next:help') ?></div>
<br />
<?php echo elgg_view('input/submit',array(
    'value' => elgg_echo('setup:next:button')
));
?>
</div>

</form>

</div>