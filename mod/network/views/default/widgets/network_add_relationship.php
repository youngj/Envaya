<?php
    $org = $vars['org'];
    $type = $vars['type'];
    $widget = $vars['widget'];
    $header = Relationship::msg_for_type($type, 'add_header');    
    
    ob_start();
    
    if (!$org)
    {
?>

<script type='text/javascript'>
<?php 
    echo view('js/create_modal_box');
    echo view('js/dom');
    echo view('js/xhr');
?>
var modalBox;

function searchOrg()
{
    var query = {
        'name': $('name').value,
        'email': $('email').value,
        'website': $('website').value,
        'phone_number': $('phone_number').value
    };   
    
    if (!query.name)
    {
        alert(<?php echo json_encode(__('network:blank_org')); ?>);
        return;
    }    
        
    var searching = $('searching_message');
    searching.style.display = 'block';        
        
    fetchJson('/pg/js_search?name='+encodeURIComponent(query.name)+
            '&email='+encodeURIComponent(query.email)+
            '&phone_number='+encodeURIComponent(query.phone_number)+
            '&website='+encodeURIComponent(query.website), 
        function(res) {        
            closeDialog();
        
            searching.style.display = 'none';
            
            var content = createElem('div', {className:'padded'});            
            var results = res.results || [];
            
            if (results.length == 0)
            {
                showNotFoundDialog(query, res);            
            }            
            else 
            {
                showConfirmMemberDialog(query, res);
            }                       
        }
    );      
}

function addNewOrg(invite)
{
    setSubmitted();
    $('org_guid').value = '';
    $('invite').value = invite ? '1' : '';
    document.forms[0].submit();
}

function addExistingOrg(org)
{
    setSubmitted();
    $('org_guid').value = org.guid;
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

function showNotFoundDialog(query, res)
{
    var content = createElem('div', {className:'padded selectMemberNotFound'},
        <?php echo json_encode(__('network:org_not_registered')); ?>.replace("{name}",query.name||query.email||query.website)
    );        
    
    if (query.email && res.can_invite)
    {
        var invite = createElem('input', { type: 'checkbox', id: 'invite_box', checked: 'checked', defaultChecked: 'checked' });
        
        content.appendChild(createElem('div',
            createElem('label', { 'for': 'invite_box' },
                invite,
                <?php echo json_encode(__('network:invite_org')); ?>.replace("{email}",query.email)
            )
        ));
    }
    else
    {
        content.appendChild(document.createTextNode(' ' +
            <?php echo json_encode(strtr(__('network:can_add_unregistered'), array(
                '{type}' => Relationship::msg_for_type($type, 'header')
            ))); ?>
        ));
    }
    
    document.body.appendChild(modalBox = createModalBox({
        title: <?php echo json_encode($header); ?>, 
        content: content,
        okFn: function() { closeDialog(); addNewOrg(invite && invite.checked); },
        hideCancel: true,
        focus: true
    }));
}

function getOrgResultView(result)
{
    var container = createElem('div', {innerHTML:result.view});
    
    var view = container.firstChild;
    view.insertBefore(
        createElem('div', {className:'selectMemberButton'}, 
            createElem('input', {
                type:'submit',                 
                click: function() { closeDialog(); addExistingOrg(result.org); },
                value:<?php echo json_encode(__('network:add')); ?>+" \xbb"
            })
        ),
        view.firstChild        
    );
    return container;
}

function showConfirmMemberDialog(query, res)
{
    var results = res.results;

    var content = createElem('div', {className:'padded'});        
    content.appendChild(createElem('div', <?php echo json_encode(Relationship::msg_for_type($type, 'add_confirm')); ?>));
                  
    for (var i = 0; i < results.length; i++)
    {       
        content.appendChild(getOrgResultView(results[i]));
    }       
    
    content.appendChild(createElem('div',
        createElem('hr'),                    
        createElem('a', {
                href:'javascript:void(0)', 
                className: 'selectMemberNone',
                click:function() { ignoreDirty(); closeDialog(); showNotFoundDialog(query, res); }
            }, 
            <?php echo json_encode(Relationship::msg_for_type($type, 'not_shown')); ?>)
    ));
    
    document.body.appendChild(modalBox = createModalBox({
        title: <?php echo json_encode($header); ?>, 
        content: content,
        okFn: function() {
            // don't remove modal box, so any onclick events can be called as a result of the enter key.
            // IE won't call onclick events if the element is removed from the DOM
        },
        hideOk: true,
        hideCancel: true,
        focus: true
    }));                    
}

</script>

<?php
}
?>

<form method='POST' action='<?php echo $widget->get_edit_url() ?>?action=add_relationship'>
<?php

echo view('input/securitytoken');
echo view('input/hidden', array('name' => 'org_guid', 'id' => 'org_guid', 'value' => $org ? $org->guid : ''));
echo view('input/hidden', array('name' => 'invite', 'id' => 'invite')); 
echo view('input/hidden', array('name' => 'type', 'value' => $type)); 

if (!$org)
{
?>

<div class='instructions'>
<?php echo Relationship::msg_for_type($type, 'add_instructions'); ?>
</div>

<table class='inputTable' style='margin:0 auto'>
<?php echo view('widgets/network_relationship_fields'); ?>
<tr><th>
<div id='searching_message' style='display:none'>
    <?php echo __('network:searching'); ?>
</div>
</th>
<td>
<?php echo view('input/submit', array(
    'value' => __('network:add_button'),
    'attrs' => array('onclick' => "searchOrg(); return false;")
)); 
?>
</td></tr>
</table>    

<?php
    echo view('focus', array('name' => 'name'));
}
else
{
    echo view_entity($org);
    
    echo "<div style='padding-top:5px'><em>".sprintf(__('network:describe_relationship'), escape($org->name))."</em></div>";
    echo view('input/tinymce', array('name' => 'content', 'track_dirty' => true));

    echo view('input/submit', array(
        'value' => __('network:add_button'),
    ));
}
?>
</form>



<?php
    $content = ob_get_clean();
    
    echo view('section', array('header' => $header, 'content' => $content));
?>