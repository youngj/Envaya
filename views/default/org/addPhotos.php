<div class='section_content padded'>
<form method='POST' action='action/org/addPhotos'>

<?php echo elgg_view('input/securitytoken') ?>

<div id='previews'></div>
<div id='progressContainer' style='padding-bottom:5px'></div>

<label id='uploadLabel'><img src='_graphics/attach_image.gif?v2' style='vertical-align:middle' /> <?php echo elgg_echo('addphotos:label') ?></label><br />
<div id='uploadContainer'></div>

<script type='text/javascript' src='_media/swfupload.js?v3'></script>
<script type='text/javascript'>

var MultiImageUploader = makeClass(ImageUploader);

MultiImageUploader.prototype.init = function($vars)
{
    ImageUploader.prototype.init.call(this, $vars);
    this.imageCount = 0;
};

MultiImageUploader.prototype.showParsedPreviewImage = function($data, $serverData)
{
    this.imageCount++;

    this.setProgress("");

    var previews = document.getElementById(this.options.previews_id);

    var container = document.createElement('div');
    container.className = 'photoPreviewContainer';

    var imageContainer = document.createElement('div');
    imageContainer.className = 'photoPreview';
    container.appendChild(imageContainer);

    var loadingMessage = document.createElement('span');
    loadingMessage.appendChild(document.createTextNode(this.options.loading_preview_message));

    imageContainer.appendChild(loadingMessage);

    var img = document.createElement('img');

    img.style.display = 'none';

    addEvent(img, 'load', function() {
        img.style.display = 'inline';
        imageContainer.removeChild(loadingMessage);
    });

    img.src = $data[this.options.thumbnail_size].url;
    imageContainer.appendChild(img);

    var caption = document.createElement('textarea');
    caption.name = 'imageCaption' + this.imageCount;
    caption.className = 'photoCaptionInput';
    container.appendChild(caption);

    var deleteButton = document.createElement('a');
    deleteButton.className = 'photoDelete';
    deleteButton.href = "javascript:void(0)";

    addEvent(deleteButton, 'click', function() {
        $dirty = window.dirty;
        setDirty(false);
        setTimeout(function() { setDirty($dirty) }, 5);

        previews.removeChild(container);
    });

    container.appendChild(deleteButton);

    var imageNumber = document.createElement('input');
    imageNumber.type = 'hidden';
    imageNumber.name = 'imageNumber[]';
    imageNumber.value = this.imageCount;
    container.appendChild(imageNumber);

    var imageData = document.createElement('input');
    imageData.type = 'hidden';
    imageData.name = 'imageData' + this.imageCount;
    imageData.value = $serverData;
    container.appendChild(imageData);

    var clear = document.createElement('div');
    clear.style.clear = 'both';
    container.appendChild(clear);

    previews.appendChild(container);

    if (!this.iframe_mode)
    {
        this.swfupload.setButtonText('<span class="button">'+this.options.button_more_message+'</span>');

        var $file = this.swfupload.getQueueFile(0);

        if ($file)
        {
            this.startUpload($file);
        }
    }

    var submit = document.getElementById('submit');
    submit.style.display = 'block';
};

MultiImageUploader.prototype.uploadProgressHandler = function()
{
    document.getElementById('uploadLabel').style.display = 'none';
    ImageUploader.prototype.uploadProgressHandler.call(this);
};

new MultiImageUploader(<?php echo elgg_view('input/swfupload_args', array(
    'args' => array(
        'trackDirty' => true,
        'thumbnail_size' => 'small',
        'max_width' => 450,
        'max_height' => 450,
        'progress_id' => 'progressContainer',
        'placeholder_id' => 'uploadContainer',
        'previews_id' => 'previews',
        'button_more_message' => elgg_echo('addphotos:more'),
        'sizes' => json_encode(NewsUpdate::getImageSizes())
    )
)) ?>);

</script>
<div class='input'>
<?php
	echo elgg_view('input/hidden', array('internalname' => 'org_guid', 'value' => $vars['entity']->guid));

	echo elgg_view('input/hidden', array(
		'internalname' => 'uuid',
		'value' => uniqid("",true)
	));

?>

<div id='submit' style='display:none'>
<?php echo elgg_view('input/submit', array('value' => elgg_echo('publish'), 'trackDirty' => true)) ?>
</div>
</div>
</div>