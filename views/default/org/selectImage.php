<?php
    $current = $vars['current'];
    $position = $vars['position'];
    $frameId = $vars['frameId'];

    if ($current)
    {
        $defaultSizeName = $current->size;
    }
    else
    {
        $defaultSizeName = 'large';
    }

    echo elgg_view('input/hidden', array(
        'internalname' => 'imageUpload',
        'internalid' => 'imageUpload',
        'value' => ''
    ));

?>
<script type='text/javascript' src='_media/swfupload.js?v6'></script>
<span id='imageUploadContainer'></span>
<div id='imageUploadProgress' class='imageUploadProgress'></div>

<div id='imageOptionsContainer' style='display:none'>
<table>
<tr>
<td>
    <label><?php echo elgg_echo('size'); ?>:</label>
</td>
<td>
<div id='imageSizeContainer'>
    <?php
        $options = array();
        foreach (Widget::getImageSizes() as $sizeName => $size)
        {
            $options[$sizeName] = elgg_echo("size:$sizeName");
        }

        echo elgg_view('input/radio', array(
            'internalname' => 'imageSize',
            'value' => $defaultSizeName,
            'inline' => true,
            'options' => $options
        ));
    ?>
</div>
</td>
</tr>
<tr>
<td>
    <label><?php echo elgg_echo('position'); ?>:</label>
</td>
<td>
    <div id='imagePositionContainer'>
    <?php
        echo elgg_view('input/radio', array(
            'internalname' => 'imagePosition',
            'value' => $position ?: 'center',
            'inline' => true,
            'options' => array(
                'left' => elgg_echo('position:left'),
                'center' => elgg_echo('position:center'),
                'right' => elgg_echo('position:right'),
            )
        ));
    ?>
    </div>
</tr>
</table>
</div>

<script type='text/javascript'>
    var uploader = new SingleImageUploader(<?php echo elgg_view('input/swfupload_args', array(
        'args' => array(
            'trackDirty' => false,
            'thumbnail_size' => 'small',
            'max_width' => 520,
            'max_height' => 520,
            'progress_id' => 'imageUploadProgress',
            'placeholder_id' => "imageUploadContainer",
            'result_id' => 'imageUpload',
            'sizes' => json_encode(Widget::getImageSizes())
        )
    )) ?>);

    var images = null;

    uploader.onImageComplete = function($images) {
        images = $images;

        var optionsContainer = document.getElementById('imageOptionsContainer');
        optionsContainer.style.display = 'block';

        var imageSizeContainer = document.getElementById('imageSizeContainer');
        var radios = imageSizeContainer.getElementsByTagName('input');

        var lastVisible = null;
        var hiddenChecked = false;
        for (var i = 0; i < radios.length; i++)
        {
            var radio = radios[i];
            var visible = $images[radio.value];
            if (visible)
            {
                lastVisible = i;
            }
            else if (radio.checked)
            {
                hiddenChecked = true;
            }
            radio.parentNode.style.display = visible ? 'inline' : 'none';
        }
        if (hiddenChecked && lastVisible)
        {
            radios[lastVisible].checked = radios[lastVisible].defaultChecked = true;
        }

        resizeFrame();
    };

    function resizeFrame()
    {
        setTimeout(function() {
            var parentDoc = window.parent.document;
            var iframe = parentDoc.getElementById(<?php echo json_encode($frameId); ?>);
            if (iframe)
            {
                var height = 50;
                if (images && images.small)
                {
                    var imageHeight = parseInt(images.small.height);
                    if (isNaN(imageHeight))
                    {
                        imageHeight = 150;
                    }
                    height = imageHeight + 80;
                }

                iframe.style.height = height+"px";

                var loading = parentDoc.getElementById(<?php echo json_encode($frameId."_loading"); ?>);
                loading.style.display = 'none';
            }
        }, 1);
    }

    function getSelectedImage()
    {
        if (!images)
        {
            return null;
        }

        var imageSizeContainer = document.getElementById('imageSizeContainer');

        var radios = imageSizeContainer.getElementsByTagName('input');
        for (var i = 0; i < radios.length; i++)
        {
            var radio = radios[i];
            if (radio.checked)
            {
                return images[radio.value];
            }
        }

        return null;
    }

    function getSelectedPosition()
    {
        var imageSizeContainer = document.getElementById('imagePositionContainer');

        var radios = imageSizeContainer.getElementsByTagName('input');
        for (var i = 0; i < radios.length; i++)
        {
            var radio = radios[i];
            if (radio.checked)
            {
                return radio.value;
            }
        }
    }

    <?php
    if ($current)
    {
        $fileGroup = get_file_group_json($current->getFilesInGroup());
        ?>
        uploader.swfupload.uploadSuccess(null, <?php echo json_encode($fileGroup) ?>);
        <?php
    }
    else
    {
        ?>
        resizeFrame();
        <?php
    }
    ?>
</script>

