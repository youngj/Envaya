<?php
    $current = $vars['current'];
    $frameId = $vars['frameId'];

    echo view('input/swfupload', array(
        'internalname' => 'docUpload',
        'internalid' => 'docUpload',
        'jsname' => 'uploader',
        'swfupload_class' => "FileUploader",
        'swfupload_args' => array(
            'post_params' => array('mode' => 'scribd')
        )
    ));    
?>
<script type='text/javascript' src='http://www.scribd.com/javascripts/view.js'></script>
<div id='scribd'></div>

<script type='text/javascript'>
    var uploader = window.uploader;
    var uploadedFile = null;

    uploader.onError = function($error) {
    
        uploadedFile = null;
    
        resizeFrame();
    };
    
    uploader.onComplete = function($files) {
    
        uploadedFile = $files.original;
        
        setTimeout(function() {        
            var doc = scribd.Document.getDoc(uploadedFile.docid, uploadedFile.accesskey);
            doc.addParam('jsapi_version', 1);
            doc.addEventListener('iPaperReady', function(e){
                doc.api.setZoom(1);
            });
            doc.write('scribd');
        }, 100);
       
        resizeFrame();
    };

    function resizeFrame()
    {
        setTimeout(function() {
            var parentDoc = window.parent.document;
            var iframe = parentDoc.getElementById(<?php echo json_encode($frameId); ?>);
            if (iframe)
            {
                iframe.style.height = uploadedFile ? "300px" : "50px";
                
                var loading = parentDoc.getElementById(<?php echo json_encode($frameId."_loading"); ?>);
                loading.style.display = 'none';
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