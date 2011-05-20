<?php
    $widget = $vars['widget'];
    echo view('js/xhr');
    echo view('js/create_modal_box');
?>

var loadLinkInfo = (function() {
    var loading = false;
    
    function setLoading($loading)
    {
        loading = $loading;
        $('loading_msg').innerHTML = $loading ? <?php echo json_encode(__('loading')); ?> : '\xa0';
    }
    
    function linkError(res)
    {
        setLoading(false);
        alert(res.error);
    }            

    return function(url, successFn)
    {    
        function linkComplete(res)
        {
            setLoading(false);

            if (res.error)
            {
                linkError(res);
            }
            else
            {   
                successFn(res);
            }
        }    
    
        if (!url)
        {
            alert(<?php echo json_encode(__('widget:links:blank')); ?>);
            return;
        }
     
        if (loading) 
            return;     
        setLoading(true);
        
        var xhr = getXHR(linkComplete, linkError);
        
        asyncPost(xhr, <?php echo json_encode($widget->get_edit_url()); ?>, {
            action: 'linkinfo_js',
            url: url
        });    
    }    
})();