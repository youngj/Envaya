<?php
    $value = @$vars['value'];
    $valueIsHTML = isset($vars['valueIsHTML']) ? $vars['valueIsHTML'] : true;
    $internalname = $vars['internalname'];
    $widthCSS = @$vars['width'] ? "width:{$vars['width']}px;" : '';
    $heightCSS = @$vars['height'] ? "height:{$vars['height']}px;" : '';

    global $CONFIG;
    global $TINYMCE_INCLUDE_COUNT;
    if (!isset($TINYMCE_INCLUDE_COUNT))
    {
        $TINYMCE_INCLUDE_COUNT = 0;
    }
    else
    {
        $TINYMCE_INCLUDE_COUNT++;
    }

    echo "<div class='input-textarea' style='padding-bottom:15px' id='tinymce_loading$TINYMCE_INCLUDE_COUNT'>".__("Loading...")."</div>";

    if (!$TINYMCE_INCLUDE_COUNT)
    {
        ?>
        <script type='text/javascript' src='_media/tiny_mce/tiny_mce.js?v<?php echo $CONFIG->cache_version ?>'></script>
        <?php
    }

    echo view("input/longtext", array(
        'internalname' => $internalname,
        'internalid' => "content_html$TINYMCE_INCLUDE_COUNT",
        'trackDirty' => true,
        'js' => "style='display:none;{$widthCSS}{$heightCSS}'",
        'value' => $valueIsHTML ? $value : view('output/longtext', array('value' => $value))));
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

    var loading = document.getElementById('tinymce_loading<?php echo $TINYMCE_INCLUDE_COUNT ?>');
    loading.style.display = 'none';

    var textarea = document.getElementById('content_html<?php echo $TINYMCE_INCLUDE_COUNT ?>');
    textarea.style.display = 'block';

    tinyMCE.init({
        setup : function(ed) {

            ed.onBeforeGetContent.add(function(ed, o) {
                var body = ed.getBody();
                var paragraphs = body.getElementsByTagName('p');
                for (var i = 0; i < paragraphs.length - 1; i++)
                {
                    paragraphs[i].className = '';
                }
                if (paragraphs.length > 0)
                {
                    paragraphs[i].className = 'last-paragraph';
                }
            });

            ed.onDblClick.add(function(ed, e) {
                if (e.target)
                {
                    if (e.target.nodeName == 'IMG')
                    {
                        ed.execCommand('mceImage');
                    }
                    else if (e.target.nodeName == 'A')
                    {
                        ed.execCommand('mceLink');
                    }
                }
            });

            ed.onChange.add(function(ed, l) {
                if (ed.isDirty())
                {
                    setDirty(true);
                }
            });

        },
        content_css: "/_css/tinymce.css?v<?php echo $CONFIG->cache_version ?>",
        editor_css: '/_css/tinymce_ui.css?v<?php echo $CONFIG->cache_version ?>',
        mode : "exact",
        theme_advanced_buttons1 : "bold,italic,underline,bullist,numlist,outdent,indent,blockquote,link,image,|,formatselect<?php echo (Session::isadminloggedin()) ? ",|,code" : '' ?>",
        language: '',
        relative_urls : false,
        elements: "content_html<?php echo $TINYMCE_INCLUDE_COUNT ?>",
        <?php if ($TINYMCE_INCLUDE_COUNT == 0) { ?>
        auto_focus: "content_html<?php echo $TINYMCE_INCLUDE_COUNT ?>",
        <?php } ?>
        theme : "-advanced"
    });

})();
</script>