<html>
<?php
    $image = get_input('image');
?>
<form id='form' method='POST' enctype='multipart/form-data' action='/pg/upload?iframe=1'>
    <body style='padding:0px;margin:0px'>
    <?php

    echo view('input/file', array(
        'internalname' => 'file',
        'internalid' => 'file',
        'js' => "onchange='fileChanged()'"
    ));

    echo view('input/hidden', array(
        'internalname' => 'image',
        'value' => $image ? '1' : ''
    ));
    
    
    echo view('input/hidden', array(
        'internalname' => 'sizes',
        'value' => get_input('sizes')
    ));

    echo view('input/hidden', array(
        'internalname' => 'swfupload',
        'internalid' => 'swfupload',
        'value' => get_input('swfupload')
    ));


    ?>

    <script type='text/javascript'>
    var swfupload = window.parent.SWFUpload.instances[document.getElementById('swfupload').value];

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
        var form = document.getElementById('form');
        var file = document.getElementById('file');

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
