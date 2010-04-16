<?php
    $org = $vars['org'];

    ob_start();
    
    echo elgg_view('input/longtext', 
        array(
            'internalname' => 'blogbody', 
            'trackDirty' => true,
            'js' => "style='height:100px'",            
        )
    );

    echo elgg_view('input/submit', 
        array('internalname' => 'submit', 
            'class' => "submit_button addUpdateButton",
            'trackDirty' => true,
            'value' => elgg_echo('publish'))); 

    
    echo elgg_view('input/hidden', array(
        'internalname' => 'container_guid', 
        'value' => $org->guid
    ));
                    
    ?>
<script type='text/javascript'>

function showAttachImage($show)
{
    $dirty = window.dirty;
    setDirty(false);
    setTimeout(function() { setDirty($dirty) }, 5);

    var attachImage = document.getElementById('attachImage');
    var attachControls = document.getElementById('attachControls');
    
    if ($show)
    {
        attachImage.style.display = 'block';
        attachControls.style.display = 'none';
    }
    else
    {
        attachImage.style.display = 'none';
        attachControls.style.display = 'block';
        
        var imageUpload = document.getElementById('imageUpload');
        imageUpload.value = '';
    }
}
</script>


<div id='attachControls'>
    <a href='javascript:void(0)' onclick='showAttachImage(true)'><img src='_graphics/attach_image.gif' /></a>
    <a href='javascript:void(0)' onclick='showAttachImage(true)'><?php echo elgg_echo('dashboard:attach_image') ?></a>    
</div>    
<div id='attachImage' style='display:none'>
    <a class='attachImageClose' href='javascript:void(0)' onclick='showAttachImage(false)'></a>    
    <span class='help'><?php echo elgg_echo('dashboard:select_image') ?></span>    
           
    <?php echo elgg_view('input/swfupload_image', array(
        'internalname' => 'image',
        'trackDirty' => true,
        'sizes' => NewsUpdate::getImageSizes()
    )) ?>
    
</div>

    <?php
    
    $formBody = ob_get_clean();
    
    echo elgg_view('input/form', array(
        'internalid' => 'addPostForm',
        'action' => "action/org/addPost", 
        'enctype' => "multipart/form-data", 
        'body' => $formBody, 
    ));
