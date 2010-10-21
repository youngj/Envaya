<script type='text/javascript'>

function showAttachImage($show)
{
    $dirty = window.dirty;
    setDirty(false);
    setTimeout(function() { setDirty($dirty) }, 5);

    if (!window.tinyMCE)
    {
        return;
    }

    setTimeout(function() {
        tinyMCE.activeEditor.execCommand("mceImage");
    }, 1);
}
</script>

<div id='attachControls'>
    <a href='javascript:void(0)' onclick='showAttachImage()'><img src='/_graphics/attach_image.gif?v2' /></a>
    <a href='javascript:void(0)' onclick='showAttachImage()'><?php echo __('dashboard:attach_image') ?></a>
</div>