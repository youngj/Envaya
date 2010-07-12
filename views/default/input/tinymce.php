<?php
    $value = $vars['value'];
    $valueIsHTML = isset($vars['valueIsHTML']) ? $vars['valueIsHTML'] : true;
    $internalname = $vars['internalname'];

    global $TINYMCE_INCLUDE_COUNT;
    if (!isset($SWFUPLOAD_INCLUDE_COUNT))
    {
        $TINYMCE_INCLUDE_COUNT = 0;
    }
    else
    {
        $TINYMCE_INCLUDE_COUNT++;
    }

    echo "<div class='input-textarea' style='padding-bottom:15px' id='tinymce_loading$TINYMCE_INCLUDE_COUNT'>".elgg_echo("Loading...")."</div>";

    if (!$TINYMCE_INCLUDE_COUNT)
    {
        ?>
        <script type='text/javascript'>
            var script = document.createElement('script');
            script.type = 'text/javascript';
            script.src = '_media/tiny_mce/tiny_mce.js?v16';
            document.getElementsByTagName("head").item(0).appendChild(script);
        </script>
        <?php
    }

    echo elgg_view("input/longtext", array(
        'internalname' => $internalname,
        'internalid' => "content_html$TINYMCE_INCLUDE_COUNT",
        'trackDirty' => true,
        'js' => "style='display:none'",
        'value' => $valueIsHTML ? $value : elgg_view('output/longtext', array('value' => $value))));
?>

<script type="text/javascript">

(function() {

    function tryInitTinyMCE()
    {
        if (!window.tinyMCE)
        {
            return;
        }

        clearInterval(checkTinyMCEInterval);

        tinyMCE.addI18n('en.advanced', <?php
            $prefix = 'tinymce:';
            $lenPrefix = strlen($prefix);

            $res = array();

            foreach (get_language_keys_by_prefix($prefix) as $key)
            {
                $res[substr($key, $lenPrefix)] = elgg_echo($key);
            }

            echo json_encode($res);
        ?>);

        tinyMCE.init({
            setup : function(ed) {

                var loading = document.getElementById('tinymce_loading<?php echo $TINYMCE_INCLUDE_COUNT ?>');
                loading.style.display = 'none';

                var textarea = document.getElementById('content_html<?php echo $TINYMCE_INCLUDE_COUNT ?>');
                textarea.style.display = 'block';

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
            content_css: "/_css/tinymce.css?v2",
            mode : "exact",
            language: '',
            relative_urls : false,
            elements: "content_html<?php echo $TINYMCE_INCLUDE_COUNT ?>",
            theme : "advanced"
        });

    }

    var checkTinyMCEInterval = setInterval(tryInitTinyMCE, 250);

})();
</script>