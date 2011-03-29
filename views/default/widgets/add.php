<?php
    $org = $vars['org'];

?>
<form action='<?php echo $org->get_url() ?>/add_page' method='POST'>
<?php echo view('input/securitytoken'); ?>
<div class='input'>
<label><?php echo __('widget:title'); ?></label><br />
<?php echo view('input/text', array('name' => 'title', 'id' => 'title', 'js' => "style='width:170px' maxlength='22'")); ?>
<div class='help'><?php echo __('widget:title_help'); ?></div>
</div>

<div class='input'>
<label><?php echo __('widget:address'); ?></label>
<div class='websiteUrl'>
<?php echo $org->get_url() . "/page/" . view('input/text', array('name' => 'widget_name', 'id' => 'widget_name', 'js' => "style='width:200px'")); 
?>
</div>
</div>

<?php echo view('focus', array('id' => 'title')); ?>
<script type='text/javascript'>
(function() {

var widgetName = document.getElementById('widget_name');
var title = document.getElementById('title');
var autoFill = true;

function makeWidgetName(value)
{
    return value.toLowerCase().replace(/\s+/g, '-').replace(/[^\w\-]/g, '');
}

addEvent(title, 'keypress', function() {
    setTimeout(function() {
        if (autoFill)
        {
            widgetName.value = makeWidgetName(title.value);
        }
    }, 1);
});
addEvent(widgetName, 'keypress', function() {
    autoFill = false;
});

})();

function saveInitialDraft()
{
    document.getElementById('save_message').style.display='inline';
    setSubmitted();
    var form = document.forms[0];
    form._draft.value = '1';
    form.submit();
}

</script>

<?php 
    echo view('input/hidden', array('name' => '_draft'));
    echo view('input/tinymce',
        array(
            'name' => 'content',
            'id' => 'post_rich',
            'saveFn' => 'saveInitialDraft',            
            'trackDirty' => true
        )
    );
?>
<div><span id='save_message' style='font-weight:bold;display:none'>Saving...</span>&nbsp;</div>

<?php echo view('input/submit', array('trackDirty' => true, 'value' => __('widget:create'))); ?>

</form>
