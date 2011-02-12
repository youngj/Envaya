<?php
    $value = @$vars['value'];
    $valueIsHTML = isset($vars['valueIsHTML']) ? $vars['valueIsHTML'] : true;
    $internalname = $vars['internalname'];
    $widthCSS = @$vars['width'] ? "width:{$vars['width']}px;" : '';
    $heightCSS = @$vars['height'] ? "height:{$vars['height']}px;" : '';

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
        <script type='text/javascript' src='/_media/tiny_mce/tiny_mce.js?v<?php echo Config::get('cache_version'); ?>'></script>
        <?php
    }

    echo view("input/longtext", array(
        'internalname' => $internalname,
        'internalid' => "content_html$TINYMCE_INCLUDE_COUNT",
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

    var loading = document.getElementById('tinymce_loading<?php echo $TINYMCE_INCLUDE_COUNT ?>');
    loading.style.display = 'none';

    var textarea = document.getElementById('content_html<?php echo $TINYMCE_INCLUDE_COUNT ?>');
    textarea.style.display = 'block';

    tinyMCE.init({
        setup : function(ed) {
        
            ed.onBeforeGetContent.add(function(ed, o)
            {
                var body = ed.getBody();

                var invalidTagNames = ['meta','style','title','link'];
                for (var j = 0; j < invalidTagNames.length; j++)
                {
                    var badTags = body.getElementsByTagName(invalidTagNames[j]), badTagsCopy = [];

                    for (var i = 0; i < badTags.length; i++)
                    {
                        badTagsCopy.push(badTags[i]);
                    }
                    for (var i = 0; i < badTagsCopy.length; i++)
                    {
                        removeElem(badTagsCopy[i]);
                    }
                }

                var paragraphs = body.getElementsByTagName('p');
                for (var i = 0; i < paragraphs.length - 1; i++)
                {
                    paragraphs[i].className = '';
                }
                if (paragraphs.length > 0)
                {
                    paragraphs[i].className = 'last-paragraph';

                    if (paragraphs[0].childNodes.length == 0)
                    {
                        removeElem(paragraphs[0]);
                    }
                }
            });

            ed.onDblClick.add(function(ed, e) {
                var target = e.target;
                if (target)
                {
                    if (target.nodeName == 'IMG')
                    {
                        if (target.className.indexOf('scribd_placeholder') != -1)
                        {
                            ed.execCommand('mceDocument');
                        }
                        else
                        {
                            ed.execCommand('mceImage');
                        }
                    }
                    else if (target.nodeName == 'A')
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
        content_css: "<?php echo Config::get('url') ?>_css/tinymce.css?v<?php echo Config::get('cache_version') ?>",
        editor_css: '<?php echo Config::get('url') ?>_css/tinymce_ui.css?v<?php echo Config::get('cache_version') ?>',
        mode : "exact",
        theme_advanced_buttons1 : "bold,italic,underline,bullist,numlist,outdent,indent,blockquote,link,image,document,|,justifyleft,justifycenter,justifyright,|,formatselect<?php echo (Session::isadminloggedin()) ? ",|,code" : '' ?>",
        language: '',
        relative_urls : false,
        remove_script_host: false,
        elements: "content_html<?php echo $TINYMCE_INCLUDE_COUNT ?>",
        <?php if ($TINYMCE_INCLUDE_COUNT == 0) { ?>
        auto_focus: "content_html<?php echo $TINYMCE_INCLUDE_COUNT ?>",
        <?php } ?>
        theme : "-advanced"
    });

})();
</script>