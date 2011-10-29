<div class='section_content padded'>
<?php        
    echo "<div style='float:right;height:60px;text-align:right;'>";
    echo "Upload Image File: ";

    echo view('input/uploader', array(
        'name' => 'imageUpload',
        'id' => 'imageUpload',
        'jsname' => 'uploader',
        'uploader_args' => array(
            'file_types' => implode(";", 
                array_map(function($ext) { return "*.$ext"; }, array('png','gif','jpg'))
            ),
            'file_types_description' => 'Images',
        )
    ));
    echo "<span id='uploadedUrl' style='font-size:10px'></span>";
    echo "</div>";

    $user = $vars['user'];    
    
    $customizable_views = $vars['customizable_views'];
    $current_view = $vars['current_view'];
    
    echo "Templates: ";
    foreach ($customizable_views as $customizable_view => $name)
    {        
        if ($current_view == $customizable_view)
        {
            echo "<strong>$name</strong> ";
        }
        else
        {
            echo "<a href='{$org->get_url()}/custom_design?current_view={$customizable_view}'>{$name}</a> ";
        }        
    }    
    echo "<br />";
    echo "<br />";
?>
<script type='text/javascript'>
    var uploader = window.uploader;      
    uploader.onComplete = function($files) {   
        console.log($files);
        $('uploadedUrl').innerHTML = $files[0].url;
    };
</script>
<?php
    echo "<br />";
    
    $custom_views = $org->get_design_setting('custom_views');
    $template = @$custom_views[$current_view];
    
    echo "<form method='POST' action='{$org->get_url()}/custom_design'>";
    echo "<h3>".escape($customizable_views[$current_view])."</h3>";
    echo view('input/securitytoken');
    echo view('input/hidden', array('id' => 'current_view', 'name' => 'current_view', 'value' => $current_view));
    
    echo view('input/longtext', array(
        'id' => 'template',
        'name' => 'template',
        'value' => $template,
        'style' => 'height:400px',
        'track_dirty' => true,
    ));
    echo view('focus', array('name' => 'template'));
    echo view('input/button', array(
        'type' => 'button',
        'value' => __('savechanges'),
        'attrs' => array('onclick' => 'saveChanges()'),
    ));

    echo "<span id='save_message'>";
    echo "</span>";
    
    echo "</form>";    
?>
</div>
<script type='text/javascript' src='/_media/jquery-1.6.2.min.js'></script>
<script type='text/javascript' src='/_media/tabby.js'></script>
<script type='text/javascript'>
    jQuery.noConflict();
    jQuery("#template").tabby();
    <?php echo view('js/xhr'); ?>
    
    function saveChanges()
    {
        setDirty(false);
        
        $('save_message').innerHTML = 'Saving...';
        
        var xhr = jsonXHR(changesSaved, saveError);
        asyncPost(xhr, document.forms[0].action, {
            current_view: $('current_view').value,
            template: $('template').value,
        });
        
        return false;
    }
    
    function saveError(res)
    {
        $('save_message').innerHTML = '';
        alert(res.error);
    }
    
    function changesSaved(res)
    {
        $('save_message').innerHTML = 'Template saved.';
        setTimeout(function() {
            $('save_message').innerHTML = '';
        },3000);
    }
    
    addEvent(document.body, 'keydown', function(e)
    {                
        var ch = e.which || e.keyCode;
        if (e.ctrlKey && ch == 83)
        {
            if (e.preventDefault)
            {
                e.preventDefault();
            }
            if (e.stopPropagation)
            {
                e.stopPropagation();
            }                
            
            saveChanges();
        }        
    });    
      
</script>