var FileUploader = makeClass();

FileUploader.prototype.init = function($vars)
{
    this.options = $vars;
    var uploader = this.uploader = new plupload.Uploader(this.getPluploadOptions());
    
    var $self = this;
    
    uploader.bind('Error', function(up, error) {
        function translateMessage() 
        {
            switch (error.code)
            {
                case plupload.FILE_SIZE_ERROR:
                    return $self.options.size_error_message;
                case plupload.FILE_EXTENSION_ERROR:
                    return $self.options.format_error_message || error.message;
                default:
                     return error.message;
            }
        }
        return $self.setProgress($self.options.queue_error_message + " " + translateMessage());
    });
    
    uploader.bind('QueueChanged', function(up) {
        $self.fileDialogCompleteHandler();
    });
    
    uploader.bind('BeforeUpload', function(up, file, serverData) {
        $self.inUpload = true;        
        $self.setProgress($self.options.upload_progress_message);
    });    
    
    uploader.bind('UploadProgress', function(up, file) {
        if ($self.inUpload)
        {
            $self.uploadProgressHandler(file);
        }
    });

    uploader.bind('FileUploaded', function(up, file, serverData) {
        $self.inUpload = false;
        $self.uploadSuccessHandler(file, serverData.response);
    });
    
    uploader.init();
    
    this.setProgress(this.options.initial_message);
};

FileUploader.prototype.getPluploadOptions = function()
{        
    var opts = this.options;
    
    // backwards compatibility for JS hardcoded in Envaya Reports
    if (opts.placeholder_id)
    {
        opts.browse_id = opts.placeholder_id + "_browse";        
        $(opts.placeholder_id).appendChild(createElem('a', {id: opts.browse_id, href:'javascript:void(0)'}, "Browse"));
        opts.container_id = opts.placeholder_id;
    }

    return {
        runtimes : opts.runtimes || "flash,html5,html4",
        browse_button : opts.browse_id,
        container: opts.container_id,
        max_file_size : '10mb',
        browse_button_hover: "upload_hover",
        url : '/pg/upload',
        multi_selection: opts.multi_selection,
        required_features: 'multipart',
        multipart : true,
        multipart_params: opts.post_params,
        resize : ((opts.max_width) ? 
            {width : opts.max_width, height : opts.max_height, quality : 75} : null),
        flash_swf_url : '/_media/plupload.flash.swf?v10',
        filters : ((opts.file_types) ? [{   
            title : opts.file_types_description, 
            extensions : opts.file_types
        }] : [])
    };
};        

FileUploader.prototype.fileDialogCompleteHandler = function()
{
    this.setProgress(this.options.processing_message);    
    this.uploader.start();
};    
    
FileUploader.prototype.setProgress = function($html)
{
    $(this.options.progress_id).innerHTML = $html; 
};

FileUploader.prototype.uploadProgressHandler = function(file)
{
    var msg;

    if (!file)
    {
        return;
    }
    if (file.loaded == file.size)
    {
        msg = this.options.server_processing_message;
    }
    else
    {
        msg = this.options.upload_progress_message;

        if (file.size)
        {
            msg += " " + Math.floor(100 * file.loaded / file.size) + "%";
        }    
    }    

    this.setProgress(msg); 
};

FileUploader.prototype.uploadSuccessHandler = function($file, $serverData)
{
    if (this.options.track_dirty)
    {
        setDirty(true);
    }
    this.showPreviewJson($serverData);            
}; 
               
FileUploader.prototype.showPreviewJson = function($json) 
{
    var $files = _eval($json);

    if ($files.error)
    {
        this.setProgress($files.error);
        if (this.onError)
        {
            this.onError($files.error);
        }        
        
        return false;
    }
    else
    {
        this.setProgress('');
        this.showPreview($files, $json);
        if (this.onComplete)
        {
            this.onComplete($files);
        }
                
        return true;
    }    
};

FileUploader.prototype.showPreview = function($files, $json) {};
    
FileUploader.prototype.getFileByProp = function(files, prop, value)
{
    for (var i = 0; files && i < files.length; i++)
    {
        var file = files[i];
        if (file[prop] == value)
        {
            return file;
        }
    }
    return null;
};

var SingleImageUploader = makeClass(FileUploader);

SingleImageUploader.prototype.init = function($vars)
{    
    FileUploader.prototype.init.call(this, $vars);

    var prevValue = $($vars.result_id).value;
    if (prevValue)
    {
        this.showPreviewJson(prevValue);
    }    
};

SingleImageUploader.prototype.reset = function()
{
    this.setProgress('');
    $(this.options.result_id).value = '';
};

SingleImageUploader.prototype.getCurrentImage = function()
{
    var imageJson = $(this.options.result_id).value;
    return imageJson ? _eval(imageJson) : null;    
};
    
SingleImageUploader.prototype.showPreview = function($images, $json)
{
    $(this.options.result_id).value = $json;
    
    var progress = $(this.options.progress_id);

    var loadingMessage = createElem('span', this.options.loading_preview_message);

    removeChildren(progress);
    progress.appendChild(loadingMessage);
    progress.style.display = 'block';
    
    var imgId = this.options.img_id;
    
    var img = imgId ? $(imgId) : createElem('img');

    img.style.visibility = 'hidden';

    addEvent(img, 'load', function() {
        img.style.visibility = 'visible';
        removeElem(loadingMessage);
    });

    var $image = this.getFileByProp($images, 'size', this.options.thumbnail_size);
    img.src = $image.url;
    
    if (!imgId)
    {
        progress.appendChild(img);    
    }
};
