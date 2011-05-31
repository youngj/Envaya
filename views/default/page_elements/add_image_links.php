<script type='text/javascript'>
<?php
    if ($INCLUDE_COUNT == 0)
    {
        include_js('image_links.js');
    }
?>
addImageLinks($('<?php echo $vars['id']; ?>'));
</script>