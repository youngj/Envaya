<div id='previews'></div>
<div id='progressContainer' style='padding-bottom:5px'></div>

<label id='uploadLabel'><img src='/_graphics/attach_image.gif?v2' style='vertical-align:middle' /> <?php echo __('upload:photos:label') ?></label><br />
<div id='uploadContainer'></div>

<?php echo view('js/swfupload'); ?>
<script type='text/javascript'>
<?php 
    echo view('js/dom'); 
    echo view('js/class'); 
?>

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

    var previews = $(this.options.previews_id);

    var container = createElem('div', {className:'photoPreviewContainer'});

    var imageContainer = createElem('div', {className:'photoPreview'});
    container.appendChild(imageContainer);

    var loadingMessage = createElem('span', this.options.loading_preview_message);

    imageContainer.appendChild(loadingMessage);

    var img = createElem('img');

    img.style.display = 'none';

    addEvent(img, 'load', function() {
        img.style.display = 'inline';
        imageContainer.removeChild(loadingMessage);
    });

    var thumbnailInfo = this.getFileByProp($data, 'size', this.options.thumbnail_size);
    
    img.src = thumbnailInfo.url;
    imageContainer.appendChild(img);

    container.appendChild(createElem('textarea', {
        name: 'imageCaption' + this.imageCount,
        className: 'photoCaptionInput'
    }));

    container.appendChild(createElem('a',{
        className:'photoDelete', 
        href:'javascript:void(0)',
        click: function() {
            ignoreDirty();
            previews.removeChild(container);
        }
    }));

    container.appendChild(createElem('input', {
        type:'hidden',
        name:'imageNumber[]',
        value: this.imageCount
    }));

    container.appendChild(createElem('input', {
        type: 'hidden',
        name: 'imageData' + this.imageCount,
        value: $json
    }));    

    var clear = createElem('div');
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

    var submit = $('submit');
    submit.style.display = 'block';
};

MultiImageUploader.prototype.uploadProgressHandler = function()
{
    $('uploadLabel').style.display = 'none';
    ImageUploader.prototype.uploadProgressHandler.call(this);
};

new MultiImageUploader(<?php echo view('input/swfupload_args', array(
    'args' => array(
        'track_dirty' => true,
        'thumbnail_size' => 'small',
        'max_width' => 540,
        'max_height' => 1080,
        'progress_id' => 'progressContainer',
        'placeholder_id' => 'uploadContainer',
        'previews_id' => 'previews',
        'no_flash_message' => view('upload/recommend_flash_message'),                
        'button_more_message' => __('upload:photos:more'),
        'post_params' => array(
            'sizes' => json_encode(Widget::get_image_sizes())
        )
    )
)) ?>);

</script>
<div class='input'>
<div id='submit' style='display:none'>
<?php echo view('input/submit', array('value' => __('publish'), 'track_dirty' => true)) ?>
</div>
</div>
