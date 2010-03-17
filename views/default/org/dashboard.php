<?php
    $org = $vars['org'];

    ob_start();
    
    echo elgg_view('input/longtext', 
        array(
            'internalname' => 'blogbody', 
            'js' => "style='height:100px'",            
        )
    );
    
    echo elgg_view('input/hidden', array(
        'internalname' => 'container_guid', 
        'value' => $org->guid
    ));
        
    echo elgg_view('input/submit', 
        array('internalname' => 'submit', 
            'class' => "submit_button addUpdateButton",
            'value' => elgg_echo('blog:publish'))); 
            
    ?>
<script type='text/javascript'>

function showAttachImage($show)
{
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
    <a href='javascript:void(0)' onclick='showAttachImage(true)'><?php echo elgg_echo('dashboard:attach_image') ?></a>    
</div>    
<div id='attachImage' style='display:none'>
    <a class='attachImageClose' href='javascript:void(0)' onclick='showAttachImage(false)'></a>    
    <div class='help'><?php echo elgg_echo('dashboard:select_image') ?>        
    </div>
    <?php echo elgg_view('input/file', array('internalid' => 'imageUpload', 'internalname' => 'image')) ?>    
    
</div>

    <?php
    
    $formBody = ob_get_clean();
    
    $form = elgg_view('input/form', array(
        'action' => "action/news/add", 
        'enctype' => "multipart/form-data", 
        'body' => $formBody, 
    ));
 
    echo elgg_view_layout('section', elgg_echo("dashboard:add_update"), $form);    

    //echo "<div class='padded'>".elgg_view("org/editPost", array('container_guid' => $org->guid))."</div>";

    $widgets = $org->getAvailableWidgets();

    $widgetList = array();
    foreach ($widgets as $widget)
    {
        $class = (!$widget->guid) ? 'class="widget_disabled"' : ''; // TODO: & enabled
        $widgetList[] .= "<a $class href='{$widget->getURL()}/edit'>".elgg_echo("widget:{$widget->widget_name}")."</a>";
    }

    echo elgg_view_layout('section', elgg_echo("dashboard:edit_widgets"), implode('<br>', $widgetList));    