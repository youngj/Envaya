<script type='text/javascript'>
<?php
    if ($INCLUDE_COUNT == 0)
    {
        readfile(Config::get('path').'_media/inline_js/image_links.js');
    }
?>
addImageLinks($('<?php echo $vars['id']; ?>'));
</script>