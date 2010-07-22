<html>

<form id='form' method='POST' enctype='multipart/form-data' action='/pg/upload?iframe=1'>
    <body style='padding:0px;margin:0px'>

    <?php

    echo elgg_view('input/file', array(
        'internalname' => 'file',
        'internalid' => 'file',
        'js' => "onchange='fileChanged()'"
    ));

    echo elgg_view('input/hidden', array(
        'internalname' => 'sizes',
        'value' => get_input('sizes')
    ));

    echo elgg_view('input/hidden', array(
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
            var len = file.value.length;
            var position = eval(len - 4);
            var fileType = file.value.toLowerCase().substr(position, len);

            if(fileType == ".jpg" || fileType == ".png" || fileType == ".gif")
            {
                swfupload.uploadProgress();
                form.submit();
            }
            else
            {
                if (fileType == ".doc")
                {
                    var msg = "<?php echo __('upload:image:isdoc'); ?>";
                }
                {
                    var msg = "<?php echo __('upload:image:isbad'); ?>";
                }
                alert(msg);
                file.value = '';
            }

        }
    }
    </script>


</form>
</body>
</html>