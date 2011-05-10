<script type='text/javascript'>    
setTimeout(function() {<?php 
    if (@$vars['name']) { 
        echo "document.forms[0][".json_encode($vars['name'])."].focus();";
    }
    else if (@$vars['id'])
    {
        echo "$(".json_encode($vars['id']).").focus();";
    }
?>},10);
</script>