<?php
    $widget = $vars['widget'];
    $org = $widget->get_container_entity();
    $escUrl = urlencode($_SERVER['REQUEST_URI']);

    ob_start();
?>

<div class='section_header'><?php echo __("org:mission"); ?></div>
<div class='section_content padded'>
<div class='input'>

    <label><?php echo __('setup:mission') ?></label>
    <?php echo view("input/tinymce", array(
        'name' => 'content',
        'autoFocus' => true,
        'trackDirty' => true,
        'valueIsHTML' => $widget->has_data_type(DataType::HTML),
        'value' => $widget->content)) ?>
</div>
<?php echo view('input/submit', array('value' => __('savechanges'))); ?>
</div>

<div class='section_header'><?php echo __("org:sectors"); ?></div>
<div class='section_content padded'>
<div class='input'>
    <label><?php echo __("setup:sector"); ?><br /></label>
    <?php
        echo view("input/checkboxes",array(
            'name' => 'sector',
            'options' => OrgSectors::get_options(),
            'value' => $org->get_sectors()));
    ?>
    <?php echo __('setup:sector:other_specify') ?> <?php echo view('input/text', array(
    'name' => 'sector_other',
    'value' => $org->sector_other,
    'js' => 'style="width:200px"'
)) ?>
</div>
</div>

<div class='section_header'><?php echo __("org:location"); ?></div>
<div class='section_content padded'>

<label><?php echo __('setup:location') ?></label>
<div>
<?php echo __('setup:city') ?> <?php echo view('input/text', array(
    'name' => 'city',
    'js' => 'style="width:200px"',
    'trackDirty' => true,
    'value' => $org->city
)) ?>, <?php echo escape($org->get_country_text()); ?>
</div>
<div>
<?php echo __('setup:region') ?> <?php echo view('input/pulldown', array(
    'name' => 'region',
    'options' => Geography::get_region_options($org->country),
    'empty_option' => __('setup:region:blank'),
    'value' => $org->region
)) ?>
<br />
<br />
<?php
    $lat = $org->get_latitude() ?: 0.0;
    $long = $org->get_longitude() ?: 0.0;

    $zoom = $widget->zoom ?: (($lat || $long) ? 11 : 1);

    echo view("output/map", array(
        'lat' => $lat,
        'long' => $long,
        'zoom' => $zoom,
        'pin' => true,
        'edit' => true
    ));
?>

</div>
</div>

<?php
    $content = ob_get_clean();

    echo view("widgets/edit_form", array(
        'widget' => $widget,
        'body' => $content
    ));
?>