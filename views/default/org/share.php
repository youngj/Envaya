<div class='section_content padded'>
<?php
    $org = $vars['org'];
    $url = $vars['url'];
    
    $network = $org->get_widget_by_class('Network');
?>
<script type='text/javascript'>
<?php 
    echo view('js/create_modal_box');
    echo view('js/dom');
    echo view('js/xhr');
?>

function isRecipient(email)
{
    var input = document.forms[0].emails;
    return input.value.toLowerCase().indexOf(email.toLowerCase()) != -1;
}

function addRecipient(email)
{
    var input = document.forms[0].emails;
    if (input.value)
    {
        input.value += "; ";
    }
    input.value += email;
}

function removeRecipient(email)
{
    var input = document.forms[0].emails;
    var value = input.value;
    
    var index = value.toLowerCase().indexOf(email.toLowerCase());
    if (index != -1)
    {
        input.value = value.substring(0,index) + value.substring(index + email.length + 1);
    }
}

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
        cancelText: "Close",
        hideOk: true
    });            
    document.body.appendChild(modalBox);  
}

function addPartners()
{
    fetchJson('/<?php echo $org->username; ?>/relationship_emails_js?t=' + (new Date().getTime()), function(res) {
        var emails = res.emails;
        var input = document.forms[0].emails;
        var added = false;
        
        for (var i = 0; i < emails.length; i++)
        {
            var email = emails[i];
            if (!isRecipient(email))
            {
                addRecipient(email);
                added = true;
            }
        }
        if (!added)
        {
            var modalBox = createModalBox({
                title: <?php echo json_encode(__('share:no_partners')); ?>, 
                content: createElem('div', {className:'padded'}, 
                    <?php echo json_encode(__('share:no_partners_2')); ?>
                ),
                okFn: function() { 
                    removeElem(modalBox);
                    window.open(<?php echo json_encode($network->get_edit_url()); ?>);
                },
                focus: true
            });            
            document.body.appendChild(modalBox);              
        }
    });
}
</script>

<form method='POST' action="<?php echo $org->get_url(); ?>/share">
<?php echo view('input/securitytoken'); ?>

<div class='input'>
<label><?php echo __('share:email_label'); ?></label><br />
<?php
    echo view('input/longtext', array(
        'name' => 'emails',
        'track_dirty' => true,
        'style' => "height:40px",
        'value' => '',
    ));

    if ($org->can_edit())
    {
?>
<a href='javascript:addUsers()' id='add_users' onclick='ignoreDirty()'><?php echo __('share:add_users'); ?></a>
&middot;
<a href='javascript:addPartners()' id='add_partners' onclick='ignoreDirty()'><?php echo __('share:add_partners'); ?></a>
<?php 
    } 
?>
</div>

<div class='input'>
<label><?php echo __('share:subject_label'); ?></label><br />
<?php
    echo view('input/text', array(
        'name' => 'subject',
        'track_dirty' => true,
        'value' => $org->can_edit() ? strtr(__('share:subject'), array(
            '{name}' => $org->name, 
        )) : '',
    ));
?>
</div>


<div class='input'>
<label><?php echo __('message:message'); ?></label><br />
<?php
    echo view('input/longtext', array(
        'name' => 'message',
        'track_dirty' => true,
        'value' => '',
    ));
?>
<div>
<?php echo __('share:link').' '; ?>
<?php echo escape($url); ?>
</div>
</div>
<?php
    echo view('input/hidden', array(
        'name' => 'u',
        'value' => $url,
    ));    
    
    echo view('focus', array('name' => 'emails'));
    echo view('input/submit', array('value' => __('message:send')));
?>
</div>