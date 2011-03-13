<?php
    $value = @$vars['value'];
    $valueIsHTML = isset($vars['valueIsHTML']) ? $vars['valueIsHTML'] : true;
    $name = $vars['name'];
    $widthCSS = @$vars['width'] ? "width:{$vars['width']}px;" : '';
    $heightCSS = @$vars['height'] ? "height:{$vars['height']}px;" : '';

    $TINYMCE_INCLUDE_COUNT = $vars['include_count'];
    
    echo "<div class='input-textarea' style='padding-bottom:15px' id='tinymce_loading$TINYMCE_INCLUDE_COUNT'>".__("Loading...")."</div>";

    if (!$TINYMCE_INCLUDE_COUNT)
    {
        ?>
        <script type='text/javascript'><?php echo view('js/create_modal_box'); ?></script>
        <script type='text/javascript' src='/_media/tiny_mce/tiny_mce.js?v<?php echo Config::get('cache_version'); ?>'></script>
        <?php                
    }

    echo view("input/longtext", array(
        'name' => $name,
        'id' => "content_html$TINYMCE_INCLUDE_COUNT",
        'trackDirty' => true,
        'js' => "style='display:none;{$widthCSS}{$heightCSS}'",
        'value' => $valueIsHTML ? Markup::render_editor_html($value) : view('output/longtext', array('value' => $value))));
?>

<script type="text/javascript">
(function() {

tinyMCE.addI18n('en.advanced', <?php
    $prefix = 'tinymce:';
    $lenPrefix = strlen($prefix);

    $res = array();
    foreach (Language::get('en')->get_group('tinymce') as $key => $enVal)
    {
        $res[substr($key, $lenPrefix)] = __($key);
    }

    echo json_encode($res);
?>);

document.getElementById('tinymce_loading<?php echo $TINYMCE_INCLUDE_COUNT ?>').style.display = 'none';
document.getElementById('content_html<?php echo $TINYMCE_INCLUDE_COUNT ?>').style.display = 'block';

tinyMCE.init({
    content_css: "<?php echo Config::get('url') ?>_css/tinymce.css?v<?php echo Config::get('cache_version') ?>",
    editor_css: '<?php echo Config::get('url') ?>_css/tinymce_ui.css?v<?php echo Config::get('cache_version') ?>',
    mode: "exact",
    theme_advanced_buttons1 : "bold,italic,underline,bullist,numlist,outdent,indent,blockquote,link,image,document,|,justifyleft,justifycenter,justifyright,|,formatselect<?php echo (Session::isadminloggedin()) ? ",|,code" : '' ?>",
    language: '',
    relative_urls: false,
    remove_script_host: false,
    elements: "content_html<?php echo $TINYMCE_INCLUDE_COUNT ?>",
    <?php if (@$vars['autoFocus']) { ?>
    auto_focus: "content_html<?php echo $TINYMCE_INCLUDE_COUNT ?>",
    <?php } ?>
    theme: "-advanced",
    plugins: '-paste'
});

})();
</script>