<?php
    $docid = $vars['docid'];
    $accesskey = $vars['accesskey'];
    
    global $SCRIBD_INCLUDE_COUNT;
    
    if (!isset($SCRIBD_INCLUDE_COUNT))
    {
        $SCRIBD_INCLUDE_COUNT = 0;
        echo "<script type='text/javascript' src='http://www.scribd.com/javascripts/view.js'></script>";
    }
    else
    {
        $SCRIBD_INCLUDE_COUNT++;
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
