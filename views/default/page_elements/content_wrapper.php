<div id='main_content'>
<?php
    echo $vars['content'];
?>
</div>
<script type='text/javascript'>
<?php include_js('inline/image_links.js'); ?>
addImageLinks($('main_content'));
</script>