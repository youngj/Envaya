<script type='text/javascript'>
function addUsers()
{
    var iframe = createElem('iframe', {
        src: '/pg/browse_email'
    });
    
    var width = 620, height = 320;
    
    iframe.style.width = width + 'px';
    iframe.style.height = height + 'px';

    var modalBox = createModalBox({
        width:width,
        height:height,
        top: 150,
        title: <?php echo json_encode(__('share:add_users')); ?>, 
        content: iframe,
        cancelFn: function() { 
            removeElem(modalBox);
        },
        cancelText: <?php echo json_encode(__('close')); ?>,
        hideOk: true
    });            
    document.body.appendChild(modalBox);  
}
</script>

<a href='javascript:addUsers()' id='add_users' onclick='ignoreDirty()'><?php echo __('share:add_users'); ?></a>