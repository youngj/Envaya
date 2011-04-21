<?php
    $widget = $vars['widget'];
    $org = $widget->get_container_entity();   
    
    $lastRevision = $widget->guid ? 
        ContentRevision::query()
            ->where('entity_guid = ?', $widget->guid)
            ->order_by('time_created desc')
            ->get() 
        : null;        
        
    ?>

<script type='text/javascript'>
<?php echo view('js/save_draft', array('guid' => $widget->guid)); ?>
</script>       
<div class='input' style='padding-bottom:0px'>
    <?php     
        $labelCode = "widget:{$widget->widget_name}:label";
        $label = __($labelCode);
        if ($label != $labelCode)
        {
            echo "<label>$label</label>";
        }
        
        $helpCode = "widget:{$widget->widget_name}:help";
        $help = __($helpCode);
        if ($help != $helpCode)
        {
            echo "<div class='help'>$help</div>";
        }
    ?>
    
    <?php echo view("input/tinymce", array(
        'name' => 'content',
        'autoFocus' => true,
        'trackDirty' => true,
        'saveFn' => 'saveDraft',
        'restoreDraftFn' => 'showOlderVersions',
        'value' => $lastRevision ? $lastRevision->content : $widget->content
    )); ?>
        
    <div>
    <span id='saved_message' style='font-weight:bold;display:none'></span>&nbsp;
    </div>        
</div>
