<div class='padded section_content'>
<?php

    $widget = $vars['widget'];
    $org = $widget->getContainerEntity();


    ob_start();
?>
<script>
function togglePosition()
{
    var upload = document.getElementById('imageUpload');
    var position = document.getElementById('imagePosition');
    var deleteImage = document.getElementById('imageDelete');
    var show = upload.value || (deleteImage && !deleteImage.checked);
    position.style.display = show ? 'block' : 'none';
}
</script>

<div class='input'>
    <label><img src='_graphics/attach_image.gif?v2' style='vertical-align:middle' /> <?php echo elgg_echo('widget:image:label') ?></label><br />
    <?php echo elgg_view('input/image', array(
        'current' => ($widget->hasImage() ? $widget->getImageUrl('small') : null),
        'js' => "onchange='javascript:togglePosition()'",
        'internalname' => 'image',
        'trackDirty' => true,
        'sizes' => Widget::getImageSizes(),
        'internalid' => 'imageUpload',
        'deletename' => 'deleteimage',
        'deleteid' => 'imageDelete',
    )) ?>
</div>

<div id='imagePosition' <?php echo ($widget->hasImage() ? '' : 'style="display:none"') ?> class='input'>
    <label><?php echo elgg_echo('widget:image:position') ?></label><br />
        <?php echo elgg_view("input/radio",array(
            'internalname' => 'image_position',
            'inline' => true,
            'value' => $widget->image_position ?: 'left',
            'options' => array(
                'left' => elgg_echo('position:left'),
                'top' => elgg_echo('position:top'),
                'right' => elgg_echo('position:right'),
                'bottom' => elgg_echo('position:bottom')
             ),
        )); ?>
</div>


<div class='input'>
    <label><?php echo elgg_echo("widget:{$widget->widget_name}:label") ?></label>
    <?php
        $helpCode = "widget:{$widget->widget_name}:help";
        $help = elgg_echo($helpCode);
        if ($help != $helpCode)
        {
            echo "<div class='help'>$help</div>";
        }
        else
        {
            echo "<br />";
        }
    ?>
    <?php echo elgg_view("input/longtext", array('internalname' => 'content','internalid' => 'content_html',
        'trackDirty' => true,
        'value' => $widget->content)); ?>

</div>

<script type="text/javascript" src="_media/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">

tinyMCE.addI18n('en.advanced', <?php
    $prefix = 'wysiwyg:';
    $lenPrefix = strlen($prefix);

    $res = array();

    foreach (get_language_keys_by_prefix($prefix) as $key)
    {
        $res[substr($key, $lenPrefix)] = elgg_echo($key);
    }

    echo json_encode($res);
?>);

tinyMCE.init({
    setup : function(ed) {
        ed.onChange.add(function(ed, l) {
            if (ed.isDirty())
            {
                setDirty(true);
            }
        });
    },
    mode : "exact",
    language: '',
    elements: "content_html",
    theme : "advanced"
});
</script>


<?php
    $content = ob_get_clean();

    echo elgg_view("widgets/edit_form", array(
        'widget' => $widget,
        'body' => $content
    ));

?>
</div>