<?php
    $docid = $vars['docid'];
    $accesskey = $vars['accesskey'];
    
    $SCRIBD_INCLUDE_COUNT = $vars['include_count'];
    
    if (!$SCRIBD_INCLUDE_COUNT)
    {
        echo "<script type='text/javascript' src='http://www.scribd.com/javascripts/view.js'></script>";
    }
?>
<div id='scribd<?php echo $SCRIBD_INCLUDE_COUNT; ?>'></div>
<script type="text/javascript">
(function() {
    var doc = scribd.Document.getDoc(<?php echo (int)$docid ?>, <?php echo json_encode((string)$accesskey) ?>);
    doc.addParam('jsapi_version', 1);
    doc.addEventListener('iPaperReady', function(e){
        doc.api.setZoom(1);
    });
    doc.write('scribd<?php echo $SCRIBD_INCLUDE_COUNT; ?>');
})();
</script>
