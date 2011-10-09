<?php
    $current = $vars['current'];
    $frameId = $vars['frameId'];

    echo view('input/uploader', array(
        'name' => 'docUpload',
        'id' => 'docUpload',
        'jsname' => 'uploader',
        'uploader_args' => array(
            'multi_selection' => false,
            'file_types' => implode(",", UploadedFile::$scribd_document_extensions),
            'initial_message' => "<div class='help' style='font-size:10px'>".__('upload:max_size')."</div>",
            'file_types_description' => 'Documents',
            'post_params' => array('mode' => 'scribd')
        )
    ));    
?>
<script type='text/javascript' src='http://www.scribd.com/javascripts/view.js'></script>
<div id='scribd'></div>

<script type='text/javascript'>
    var showPreview = false;
    var uploader = window.uploader;
    var uploadedFile = null;

    var parentAPI = window.parent["frameapi_" + <?php echo json_encode($frameId); ?>];
    
    uploader.onError = function($error) {
    
        uploadedFile = null;
    
        resizeFrame();
    };
    
    uploader.onComplete = function($files) {
                  
        uploadedFile = uploader.getFileByProp($files, 'storage', 'scribd');
        
        if (showPreview)
        {        
            setTimeout(function() {        
                var doc = scribd.Document.getDoc(uploadedFile.docid, uploadedFile.accesskey);
                doc.addParam('jsapi_version', 1);
                doc.addEventListener('iPaperReady', function(e){
                    doc.api.setZoom(1);
                });
                doc.write('scribd');
            }, 100);
       
            resizeFrame();
        }
        else
        {
            setTimeout(function() {
                parentAPI.saveChanges();                
            }, 10);
        }
    };

    function resizeFrame()
    {
        setTimeout(function() {
            var iframe = parentAPI.iframe;
            if (iframe)
            {
                iframe.style.height = uploadedFile ? "300px" : "65px";
                
                parentAPI.loading.style.display = 'none';
            }
        }, 1);
    }

    function getUploadedFile() /* api for container (tinymce) to get data from frame */
    {
        return window.uploadedFile;
    }
    
    <?php
    if ($current)
    {
        $fileGroup = UploadedFile::json_encode_array($current->get_files_in_group());
        ?>
        showPreview = true;
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