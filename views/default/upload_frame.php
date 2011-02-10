<html>
<body style='padding:0px;margin:0px'>
<form id='form' method='POST' enctype='multipart/form-data' action='/pg/upload?iframe=1'>
<?php
    echo view('input/file', array(
        'internalname' => 'file',
        'internalid' => 'file',
        'js' => "onchange='fileChanged()'"
    ));   
    echo view('input/hidden_multi', array('fields' => $_GET));
?>

<script type='text/javascript'>
var swfupload = window.parent.SWFUpload.instances[<?php echo json_encode(get_input('swfupload')); ?>];

<?php
    $lastUpload = Session::get('lastUpload');
    if ($lastUpload)
    {
        Session::set('lastUpload', null);
?>
        swfupload.uploadSuccess(null, <?php echo json_encode($lastUpload) ?>);
<?php
    }
?>
function fileChanged()
{
    var form = document.getElementById('form'),
        file = document.getElementById('file');

    if (file.value)
    {
        swfupload.uploadProgress();
        form.submit();
    }
}
</script>
</form>
</body>
</html>