<div id='main_content'>
<?php
    echo $vars['content'];
?>
</div>
<div style='clear:both'></div>
<script type='text/javascript'>
<?php include_js('inline/image_links.js'); ?>
addImageLinks($('main_content'));
</script>