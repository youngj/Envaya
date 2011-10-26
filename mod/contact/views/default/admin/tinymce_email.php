<?php
    $vars['allowDocument'] = false;

    echo view('input/tinymce', $vars);
?>
<script type='text/javascript'>

tinyMCE.onAddEditor.add(function(mgr, ed) {

    ed.onBeforeGetContent.add(function(ed, o)
    {
        var body = ed.getBody();
        
        var paragraphs = body.getElementsByTagName('p');
        for (var i = 0; i < paragraphs.length; i++)
        {
            var p = paragraphs[i];
            p.style.marginBottom = '15px';
        }
    });

});
</script>