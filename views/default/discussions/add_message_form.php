<?php
    $topic = $vars['topic'];
    $reply_to = @$vars['reply_to'];
    
?>
<form method='POST' action='<?php echo $topic->get_url(); ?>/add_message'>
<?php 
    echo view('input/tinymce', array(
        'name' => 'content', 
        'trackDirty' => true, 
        'value' => $reply_to ? view('discussions/reply_message', array('message' => $reply_to)) : '',
    ));        
    
?>
<script type='text/javascript'>

tinyMCE.onAddEditor.add(function(mgr, ed) {

    var firstSet = true;
    ed.onSetContent.add(function(ed, o)
    {
        if (firstSet)
        {
<?php if ($reply_to) {  ?>   
            ed.selection.select(ed.dom.select('span')[0]); // move cursor to empty span at end of reply message
<?php } ?>
            setTimeout(function() {
                ed.focus();
            }, 10);
        }
        firstSet = false;
    });
});

</script>
<?php
    
    echo view('discussions/user_info');
    echo view('input/securitytoken');
    echo view('input/uniqid');    
    echo view('input/submit', array('value' => __('discussions:publish_message')));    
 ?>
</form>
