<?php
    $widget = $vars['widget'];
    $org = $widget->get_container_entity();
    
?>
<script type='text/javascript'>
<?php echo view('js/create_modal_box'); ?>
var modalBox;

function searchNetworkMember()
{    
    var query = {
        'name': document.getElementById('member_name').value,
        'email': document.getElementById('member_email').value,
        'website': document.getElementById('member_website').value
    };   
    
    if (!query.name && !query.email && !query.website)
    {
        alert(<?php echo json_encode(__('network:blank_member')); ?>);
        return;
    }    
        
    var searching = document.getElementById('searching_message');
    searching.style.display = 'block';        
        
    fetchJson('<?php echo $org->get_url(); ?>/search_new_member?name='+encodeURIComponent(query.name)+
        '&email='+encodeURIComponent(query.email)+
        '&website='+encodeURIComponent(query.website), 
        function(res) {        
            closeDialog();
        
            searching.style.display = 'none';
            
            var content = createElem('div', {className:'padded'});            
            var results = res.results || [];
            
            if (results.length == 0)
            {
                showNotFoundDialog(query);            
            }            
            else 
            {
                showConfirmMemberDialog(query, results);
            }                       
        }
    );      
}

function addNewOrg(invite)
{
    document.getElementById('member_guid').value = '';
    document.getElementById('member_invite').value = invite ? '1' : '';
    document.forms[0].submit();
}

function addExistingOrg(org)
{
    document.getElementById('member_guid').value = org.guid;
    document.forms[0].submit();
}

function closeDialog()
{
    if (modalBox)
    {
        removeElem(modalBox);
        modalBox = null;
    }
}

function showNotFoundDialog(query)
{
    var content = createElem('div', {className:'padded'},
        <?php echo json_encode(__('network:org_not_registered')); ?>.replace("%s",query.name||query.email||query.website)
    );        
    
    if (query.email)
    {
        var invite = createElem('input', { type: 'checkbox', id: 'invite_box', checked: 'checked', defaultChecked: 'checked' });
        
        content.appendChild(createElem('div',
            createElem('label', { 'for': 'invite_box' },
                invite,
                <?php echo json_encode(__('network:invite_org')); ?>.replace("%s",query.email)
            )
        ));
    }
    else
    {
        content.appendChild(createElem('div',
            <?php echo json_encode(__('network:org_not_registered_2')); ?>
        ));
    }
    
    document.body.appendChild(modalBox = createModalBox({
        title: <?php echo json_encode(__('network:add_member')); ?>, 
        content: content,
        okFn: function() { closeDialog(); addNewOrg(invite && invite.checked); },
        hideCancel: true,
        focus: true
    }));
}

function getOrgResultView(result)
{
    return createElem('div',
        createElem('div', {className:'selectMemberButton'}, 
            createElem('input', {
                type:'submit',                 
                click: function() { closeDialog(); addExistingOrg(result.org); },
                value:<?php echo json_encode(__('network:add_select')); ?>+" \xbb"
            })
        ),
        createElem('div', {innerHTML:result.view})
    );
}

function showConfirmMemberDialog(query, results)
{
    var content = createElem('div', {className:'padded'});        
    content.appendChild(createElem('div', <?php echo json_encode(__('network:confirm_member')); ?>));
                  
    for (var i = 0; i < results.length; i++)
    {       
        content.appendChild(getOrgResultView(results[i]));
    }       
    
    content.appendChild(createElem('div',
        createElem('hr'),                    
        createElem('a', {
                href:'javascript:void(0)', 
                click:function() { ignoreDirty(); closeDialog(); showNotFoundDialog(query); }, 
                className:'selectMemberNotShown'
            }, 
            <?php echo json_encode(__('network:member_not_shown')); ?>)
    ));
    
    document.body.appendChild(modalBox = createModalBox({
        title: <?php echo json_encode(__('network:add_member')); ?>, 
        content: content,
        hideOk: true,
        hideCancel: true,
        focus: true
    }));                    
}


</script>
<?php
    ob_start();
?>
<form method='POST' action='<?php echo $widget->get_edit_url() ?>?action=add_member'>
<?php echo view('input/securitytoken'); ?>

<div class='instructions'>
<?php echo __('network:add_member_instructions'); ?>
</div>

<?php echo view('input/hidden', array('name' => 'org_guid', 'id' => 'member_guid')); ?>
<?php echo view('input/hidden', array('name' => 'invite', 'id' => 'member_invite')); ?>

<table class='inputTable' style='margin:0 auto'>
<tr><th><?php echo __('network:member_name'); ?></th>
<td><?php echo view('input/text', array('name' => 'name', 'id' => 'member_name')); ?></td></tr>
<tr><th><?php echo __('network:member_email'); ?></th>
<td><?php echo view('input/text', array('name' => 'email', 'id' => 'member_email')); ?></td></tr>
<tr><th><?php echo __('network:member_website'); ?></th>
<td><?php echo view('input/text', array('name' => 'website', 'id' => 'member_website')); ?></td></tr>
<tr><th>&nbsp;</th>
<td>
<div id='searching_message' style='display:none;float:right;padding-top:18px'><?php echo __('network:searching'); ?></div>
<?php echo view('input/submit', array(
    'name' => '_save',
    'value' => __('network:add_button'),
    'js' => "onclick='searchNetworkMember(); return false;'"
));
?>
</td></tr>

</table>    

</form>
<?php
    $content = ob_get_clean();
    
    echo view('section', array('header' => __("network:add_member"), 'content' => $content));    
?>
<script type='text/javascript'>
setTimeout(function() {
    document.forms[0].name.focus();
},10);
</script>
