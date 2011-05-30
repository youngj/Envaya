<?php

    $saveDraft = @$vars['saveDraft'];    
    if ($saveDraft)
    {
        $entity = @$vars['entity'];
               
        $lastRevision = $entity->guid ? 
            ContentRevision::query()
                ->where('entity_guid = ?', $entity->guid)
                ->order_by('time_created desc')
                ->get() 
            : null;        
        
        if ($lastRevision)
        {
            $vars['value'] = $lastRevision->content;
        }
                
        echo "<script type='text/javascript'>";
        echo view('js/save_draft', array('guid' => $entity->guid));
        echo "</script>";
        
        $vars['saveFn'] = 'saveDraft';
        $vars['restoreDraftFn'] = 'showOlderVersions';        
    }

    $value = @$vars['value'];   
    $name = $vars['name'];
    $widthCSS = @$vars['width'] ? "width:{$vars['width']}px;" : '';
    $heightCSS = @$vars['height'] ? "height:{$vars['height']}px;" : '';        
    
    echo "<div class='input-textarea' style='padding-bottom:15px' id='tinymce_loading$INCLUDE_COUNT'>".__("Loading...")."</div>";

    if (!$INCLUDE_COUNT)
    {
        PageContext::add_header_html(
            "<style type='text/css'>"
            .file_get_contents(Config::get('path').'/_css/tinymce_ui.css')
            ."</style>"
        );
        
        foreach (Language::get('en')->get_group('tinymce') as $key => $enVal)
        {
            PageContext::add_js_string($key);
        }
        PageContext::add_js_string('loading');
        
        ?>
        
        <script type='text/javascript'><?php 
            echo view('js/create_modal_box');         
            echo view('js/dom'); 
        ?></script>
        <script type='text/javascript' src='/_media/tiny_mce/tiny_mce.js?v<?php echo Config::get('cache_version'); ?>'></script>
        <?php                
    }

    echo view("input/longtext", array(
        'name' => $name,
        'id' => "content_html$INCLUDE_COUNT",
        'trackDirty' => true,
        'js' => "style='display:none;{$widthCSS}{$heightCSS}'",
        'value' => Markup::render_editor_html($value)
    ));
    
    if ($saveDraft)
    {
        echo "<div>";
        echo "<span id='saved_message' style='font-weight:bold;display:none'></span>&nbsp;";
        echo "</div>";
    }
    
?>

<script type="text/javascript">
(function() {

$('tinymce_loading<?php echo $INCLUDE_COUNT ?>').style.display = 'none';
$('content_html<?php echo $INCLUDE_COUNT ?>').style.display = 'block';

tinyMCE.init({
    content_css: "<?php echo css_url('tinymce'); ?>",
    mode: "exact",
    theme_advanced_buttons1 : "<?php 
        echo "bold,italic,underline,bullist,numlist,outdent,indent,blockquote,link";        
        echo Session::isloggedin() ? ',image,document' : '';
        echo ",|,justifyleft,justifycenter,justifyright,|,formatselect";
        echo (@$vars['saveFn']) ? ",|,save" : "";
        echo (@$vars['restoreDraftFn']) ? ",restoredraft" : "";
        echo (Session::isadminloggedin()) ? ",|,code" : '';
    ?>",
    language: '',
    relative_urls: false,
    remove_script_host: false,
    elements: "content_html<?php echo $INCLUDE_COUNT ?>",
    <?php if (@$vars['saveFn']) { ?>
    save_draft_callback: <?php echo $vars['saveFn']; ?>,
    <?php } ?>
    <?php if (@$vars['restoreDraftFn']) { ?>
    restore_draft_callback: <?php echo $vars['restoreDraftFn']; ?>,
    <?php } ?>    
    <?php if (@$vars['autoFocus']) { ?>
    auto_focus: "content_html<?php echo $INCLUDE_COUNT ?>",
    <?php } ?>
    theme: "-advanced",
    plugins: '-paste',
    skin: 't'
});

})();
</script>
