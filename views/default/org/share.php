<div class='section_content padded'>
<?php
    $org = $vars['org'];
    $url = $vars['url'];
    
    $network = $org->get_widget_by_class('Network');
?>
<script type='text/javascript'>
<?php echo view('js/create_modal_box'); ?>

var modalBox;

function addPartners()
{
    fetchJson('/<?php echo $org->username; ?>/relationship_emails_js?t=' + (new Date().getTime()), function(res) {
        var emails = res.emails;
        var input = document.forms[0].emails;
        var added = false;
        
        for (var i = 0; i < emails.length; i++)
        {
            var email = emails[i];
            if (input.value.toLowerCase().indexOf(email) == -1)
            {
                if (input.value)
                {
                    input.value += "; ";
                }
                input.value += email;
                added = true;
            }
        }
        if (!added)
        {
            document.body.appendChild(modalBox = createModalBox({
                title: <?php echo json_encode(__('share:no_partners')); ?>, 
                content: createElem('div', {className:'padded'}, 
                    <?php echo json_encode(__('share:no_partners_2')); ?>
                ),
                okFn: function() { 
                    removeElem(modalBox);
                    window.open(<?php echo json_encode($network->get_edit_url()); ?>);
                },
                focus: true
            }));              
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
        'trackDirty' => true,
        'js' => "style='height:40px'",
        'value' => '',
    ));
?>
<?php
    if ($org->can_edit())
    {
?>
<a href='javascript:addPartners()' onclick='ignoreDirty()'><?php echo __('share:add_partners'); ?></a>
<?php } ?>
</div>

<div class='input'>
<label><?php echo __('share:subject_label'); ?></label><br />
<?php
    echo view('input/text', array(
        'name' => 'subject',
        'trackDirty' => true,
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
        'trackDirty' => true,
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