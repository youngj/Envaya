<?php
    // parameters:
    $saveDraft = false;
    $save_draft_url = null;
    $entity = null;
    $saveFn = null;
    $restoreDraftFn = null;
    $width = null;
    $height = null;
    $value = null;
    $allowDocument = $allowImage = Session::is_logged_in();
    $allowCode = Permission_UseAdminTools::has_any();    
    $autoFocus = false;
    extract($vars);

    if ($saveDraft)
    {                 
        $lastRevision = ($entity && $entity->guid) ? 
            ContentRevision::query()
                ->where('entity_guid = ?', $entity->guid)
                ->order_by('time_created desc, id desc')
                ->get() 
            : null;
        
        if ($lastRevision)
        {
            $value = $lastRevision->content;
        }
                
        echo view('js/save_draft', array(
            'guid' => ($entity ? $entity->guid : null),
            'url' => $save_draft_url,
        ));
        
        $saveFn = 'saveDraft';
        $restoreDraftFn = 'showOlderVersions';        
    }

    $widthCSS = $width ? "width:{$width}px;" : '';
    $heightCSS = $height ? "height:{$height}px;" : '';        
    
    echo "<div class='input-textarea' style='padding-bottom:15px' id='tinymce_loading$INCLUDE_COUNT'>".__("loading")."</div>";

    if (!$INCLUDE_COUNT)
    {
        PageContext::add_header_html(
            "<style type='text/css'>"
            .file_get_contents(Engine::$root.'/www/_media/css/tinymce_ui.css')
            ."</style>"
        );
        
        foreach (Language::get('en')->get_group('tinymce') as $key => $enVal)
        {
            PageContext::add_js_string($key);
        }
        PageContext::add_js_string('loading');
        
        echo view('js/create_modal_box');         
        echo view('js/dom'); 
        ?>
        <script type='text/javascript' src='/_media/tiny_mce.js?v<?php echo Config::get('build:hash:js:tiny_mce'); ?>'></script>
        <?php                
    }

    echo view("input/longtext", array(
        'name' => $name,
        'id' => "content_html$INCLUDE_COUNT",
        'track_dirty' => true,
        'style' => "display:none;{$widthCSS}{$heightCSS}",
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
    buttons1 : "<?php 
        echo "bold,italic,underline,bullist,numlist,outdent,indent,blockquote,link";        
        if ($allowImage) { echo ',image'; }
        if ($allowDocument) { echo ',document'; }
        echo ",|,justifyleft,justifycenter,justifyright,|,formatselect";
        if ($saveFn) { echo ",|,save"; }
        if ($restoreDraftFn) { echo ",restoredraft"; }
        if ($allowCode) { echo ",|,code"; }
    ?>",
    language: '',
    relative_urls: false,
    remove_script_host: false,
    elements: "content_html<?php echo $INCLUDE_COUNT ?>",
    <?php if ($saveFn) { ?>
    save_draft_callback: <?php echo $saveFn; ?>,
    <?php } ?>
    <?php if ($restoreDraftFn) { ?>
    restore_draft_callback: <?php echo $restoreDraftFn; ?>,
    <?php } ?>    
    <?php if ($autoFocus) { ?>
    auto_focus: "content_html<?php echo $INCLUDE_COUNT ?>",
    <?php } ?>
    theme: "-custom",
    plugins: '-paste',
    skin: 't'
});

})();
</script>
