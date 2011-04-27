<script type='text/javascript'>

function showAttachImage()
{
    if (window.tinyMCE)
    {       
        setTimeout(function() {
            tinyMCE.activeEditor.execCommand("mceImage");
        }, 1);
    }
}

function showAttachDocument()
{
    if (window.tinyMCE)
    {
        setTimeout(function() {
            tinyMCE.activeEditor.execCommand("mceDocument");
        }, 1);
    }
}

</script>

<div class='attachControls'>
    <a href='javascript:showAttachImage()' class='attachImage' onclick='ignoreDirty()'><?php echo __('widget:news:attach_image') ?></a>
    <a href='javascript:showAttachDocument()' class='attachDocument' onclick='ignoreDirty()'><?php echo __('widget:news:attach_document') ?></a>    
</div>