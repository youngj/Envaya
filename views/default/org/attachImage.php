<script type='text/javascript'>

function keepDirty()
{
    var $dirty = window.dirty;
    setDirty(false);
    setTimeout(function() { setDirty($dirty) }, 5);
}

function showAttachImage()
{
    keepDirty();    
    if (window.tinyMCE)
    {       
        setTimeout(function() {
            tinyMCE.activeEditor.execCommand("mceImage");
        }, 1);
    }
}

function showAttachDocument()
{
    keepDirty();
    if (window.tinyMCE)
    {
        setTimeout(function() {
            tinyMCE.activeEditor.execCommand("mceDocument");
        }, 1);
    }
}

</script>

<div class='attachControls'>
    <a href='javascript:void(0)' class='attachImage' onclick='showAttachImage()'><?php echo __('dashboard:attach_image') ?></a>
    <a href='javascript:void(0)' class='attachDocument' onclick='showAttachDocument()'><?php echo __('dashboard:attach_document') ?></a>    
</div>