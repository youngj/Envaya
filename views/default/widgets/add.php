<div class='section_content padded'>
<?php
    $org = $vars['org'];

?>
<form action='<?php echo $org->get_url() ?>/add_page' method='POST'>
<?php
    echo view('input/securitytoken');     
    echo view('widgets/edit_page_title');
    echo view('widgets/edit_page_address', array('org' => $org));
    echo view('focus', array('id' => 'title')); 
?>
<script type='text/javascript'>
(function() {

var widgetName = $('widget_name');
var title = $('title');
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
</script>

<?php 
    echo view('widgets/edit_initial_content');
    echo view('input/submit', array('trackDirty' => true, 'value' => __('widget:create'))); ?>

</form>
</div>