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

    echo view('input/image_uploader', array(
        'name' => 'imageUpload',
        'id' => 'imageUpload',
        'jsname' => 'uploader',
        'sizes' => Widget::get_image_sizes()
    ));
    
?>

<div id='imageOptionsContainer' style='display:none'>
<table>
<tr>
<td>
    <label><?php echo __('size:label'); ?>:</label>
</td>
<td>
<div id='imageSizeContainer'>
    <?php
        $options = array();
        foreach (Widget::get_image_sizes() as $sizeName => $size)
        {
            $options[$sizeName] = __("size:$sizeName");
        }

        echo view('input/radio', array(
            'name' => 'imageSize',
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
    <label><?php echo __('position:label'); ?>:</label>
</td>
<td>
    <div id='imagePositionContainer'>
    <?php
        echo view('input/radio', array(
            'name' => 'imagePosition',
            'value' => $position ?: 'center',
            'inline' => true,
            'options' => array(
                'left' => __('position:left'),
                'center' => __('position:center'),
                'right' => __('position:right'),
            )
        ));
    ?>
    </div>
</tr>
</table>
</div>

<script type='text/javascript'>
    var uploader = window.uploader;
    
    var images = null;
    var upload_error = null;
    
    var parentAPI = window.parent["frameapi_" + <?php echo json_encode($frameId); ?>];

    uploader.onError = function($error) {
        upload_error = $error;
        resizeFrame();
    };
    
    uploader.onComplete = function($images) {
        images = $images;

        var optionsContainer = $('imageOptionsContainer');
        optionsContainer.style.display = 'block';

        var imageSizeContainer = $('imageSizeContainer');
        var radios = imageSizeContainer.getElementsByTagName('input');

        var lastVisible = null;
        var hiddenChecked = false;
        for (var i = 0; i < radios.length; i++)
        {
            var radio = radios[i];
            var visible = uploader.getFileByProp($images, 'size', radio.value) != null;
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
            var iframe = parentAPI.iframe;
            if (iframe)
            {
                var height = 50;                
                var small = uploader.getFileByProp(images, 'size', 'small');
                
                if (small)
                {
                    var imageHeight = parseInt(small.height);
                    if (isNaN(imageHeight))
                    {
                        imageHeight = 150;
                    }
                    height = imageHeight + 80;
                }
                else if (upload_error)
                {
                    height = 80;
                }

                iframe.style.height = height+"px";

                parentAPI.loading.style.display = 'none';
            }
        }, 1);
    }

    function getSelectedImage()
    {
        if (!images)
        {
            return null;
        }

        var imageSizeContainer = $('imageSizeContainer');

        var radios = imageSizeContainer.getElementsByTagName('input');
        for (var i = 0; i < radios.length; i++)
        {
            var radio = radios[i];
            if (radio.checked)
            {
                return uploader.getFileByProp(images, 'size', radio.value);
            }
        }

        return null;
    }

    function getSelectedPosition()
    {
        var imageSizeContainer = $('imagePositionContainer');

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
        $fileGroup = UploadedFile::json_encode_array($current->get_files_in_group());
        ?>
        uploader.uploadSuccessHandler(null, <?php echo json_encode($fileGroup) ?>);
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