<div id='previews'></div>
<div id='progressContainer' style='padding-bottom:5px'></div>

<label id='uploadLabel'><img src='/_media/images/attach_image.gif?v2' style='vertical-align:middle' /> <?php echo __('upload:photos:label') ?></label><br />
<div id='browseContainer'>
<a href='javascript:void(0)' style='font-weight:bold' id='browseButton'><?php echo __('upload:browse'); ?></a>
</div>

<?php 
    echo view('js/uploader');
    echo view('js/dom'); 
    echo view('js/class'); 
 ?>
<script type='text/javascript'>

var MultiImageUploader = makeClass(FileUploader);

MultiImageUploader.prototype.init = function($vars)
{
    FileUploader.prototype.init.call(this, $vars);
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

    var submit = $('submit');
    submit.style.display = 'block';
};

MultiImageUploader.prototype.uploadProgressHandler = function()
{
    $('uploadLabel').style.display = 'none';
    FileUploader.prototype.uploadProgressHandler.call(this);
};

new MultiImageUploader(<?= json_encode(UploadedFile::get_uploader_args(array(
    'file_types' => implode(",", UploadedFile::$image_extensions),
    'file_types_description' => "Images",
    'track_dirty' => true,
    'thumbnail_size' => 'small',
    'max_width' => 540,
    'max_height' => 1080,
    'multi_selection' => true,
    'container_id' => 'browseContainer',
    'progress_id' => 'progressContainer',
    'browse_id' => 'browseButton',
    'previews_id' => 'previews',
    'button_more_message' => __('upload:photos:more'),
    'post_params' => array(
        'mode' => 'image',
        'sizes' => json_encode(Widget::get_image_sizes())
    )
))) ?>);

</script>
<div class='input'>
<div id='submit' style='display:none'>
<?php echo view('input/submit', array('value' => __('publish'), 'track_dirty' => true)) ?>
</div>
</div>
