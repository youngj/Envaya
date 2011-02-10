<div id='previews'></div>
<div id='progressContainer' style='padding-bottom:5px'></div>

<label id='uploadLabel'><img src='/_graphics/attach_image.gif?v2' style='vertical-align:middle' /> <?php echo __('addphotos:label') ?></label><br />
<div id='uploadContainer'></div>

<script type='text/javascript' src='/_media/swfupload.js?v25'></script>
<script type='text/javascript'>

var MultiImageUploader = makeClass(ImageUploader);

MultiImageUploader.prototype.init = function($vars)
{
    ImageUploader.prototype.init.call(this, $vars);
    this.imageCount = 0;
};

MultiImageUploader.prototype.showPreview = function($data, $json)
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
    imageData.value = $json;
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

new MultiImageUploader(<?php echo view('input/swfupload_args', array(
    'args' => array(
        'trackDirty' => true,
        'thumbnail_size' => 'small',
        'max_width' => 540,
        'max_height' => 1080,
        'progress_id' => 'progressContainer',
        'placeholder_id' => 'uploadContainer',
        'previews_id' => 'previews',
        'recommend_flash_message' => view('org/recommend_flash_message'),                
        'button_more_message' => __('addphotos:more'),
        'post_params' => array(
            'sizes' => json_encode(Widget::get_image_sizes())
        )
    )
)) ?>);

</script>
<div class='input'>
<div id='submit' style='display:none'>
<?php echo view('input/submit', array('value' => __('publish'), 'trackDirty' => true)) ?>
</div>
</div>
