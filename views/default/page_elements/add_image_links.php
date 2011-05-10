<script type='text/javascript'>
<?php
    if ($vars['include_count'] == 0)
    {
        readfile(Config::get('path').'_media/inline_js/image_links.js');
    }
?>
addImageLinks($('<?php echo $vars['id']; ?>'));
</script>