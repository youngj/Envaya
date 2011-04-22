<script type='text/javascript'>
function getPhotoUrlInput()
{
    return document.forms[0][<?php echo json_encode($vars['name']) ;?>]
}

function updatePhotoPreview()
{
    var input = getPhotoUrlInput();
    var url = input.value;
    var photo_id = document.getElementById(<?php echo json_encode($vars['photo_id']); ?>);    
    photo_id.src = url;
}
</script>
<?php 
    echo view('input/text', array(
        'name' => $vars['name'], 
        'value' => $vars['value'], 
        'js' => "onchange='updatePhotoPreview()'",
    ));     

    echo view('input/swfupload', array(
        'name' => '_file_upload',
        'swfupload_class' => 'FileUploader',
        'jsname' => 'uploader'
    ));
?>
<script type='text/javascript'>
    var uploader = window.uploader;    
    uploader.showPreview = function($files, $json) 
    {
        var input = getPhotoUrlInput();
        input.value = $files[0].url;
        updatePhotoPreview();
    };
</script>