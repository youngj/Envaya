<?php
	/**
	 * Source code editor
     */
     
    $name = null;            // html name attribute for input field
    $value = null;           // html value attribute
    $track_dirty = false;     // call setDirty when the field is changed?    
    $container_id = "codeEditor$INCLUDE_COUNT";
    $mode = 'html';
    $id = "codeValue$INCLUDE_COUNT";
    $save_fn = null;
    $js_name = null;
    
    extract($vars);
    
    $value = restore_input($name, $value, $track_dirty);
    
    $attrs = Markup::get_attrs($vars, array(
        'class' => 'input-code',
        'style' => null,
    ));    
    
    if ($name)
    {
        echo view('input/hidden', array(
            'id' => $id,
            'name' => $name,
        ));
    }
    
    echo "<div id='$container_id' ".Markup::render_attrs($attrs)."></div>";    
    echo "<script type='text/javascript' src='/_media/ace/ace.js'></script>";
    echo "<script type='text/javascript' src='/_media/ace/mode-$mode.js'></script>";
?>
<script type='text/javascript'>   
(function() {
    var editor = ace.edit(<?php echo json_encode($container_id); ?>);    
    var mode = require("ace/mode/<?php echo $mode; ?>").Mode;    
    
    var session = editor.getSession();
    
    editor.renderer.setShowPrintMargin(false);
    editor.renderer.setHScrollBarAlwaysVisible(false);
    session.setUseWrapMode(true);
    session.setWrapLimitRange(null, null);    
    session.setMode(new mode());    
    session.setValue(<?php echo json_encode($value); ?>);
    
    
    var hiddenField = $(<?php echo json_encode($id); ?>);
    session.on('change', function() {
        <?php if ($name) { ?>    
        hiddenField.value = session.getValue();
        <?php } ?>
        
        <?php if ($track_dirty) { ?>    
        setDirty(true);
        <?php } ?>
    });
    
    
    
    <?php if ($save_fn) { ?>
    editor.commands.addCommand({
        name: "save",
        bindKey: {
            win: "Ctrl-S",
            mac: "Command-S",
            sender: "editor"
        },
        exec: function() {
            <?php echo $save_fn; ?>();
        }
    });
    <?php } ?>
    
    <?php if ($js_name) { ?>
    
    window[<?php echo json_encode($js_name); ?>] = editor;
    
    <?php } ?>
})();
</script>