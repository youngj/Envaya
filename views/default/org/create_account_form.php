<?php

    $initial_email = '';
    $initial_name = '';

    $invite_code = Session::get('invite_code');
    if ($invite_code)
    {
        $invitedEmail = InvitedEmail::query()
            ->where('invite_code = ?', $invite_code)
            ->where('registered_guid = 0')
            ->get();
        if ($invitedEmail)
        {
            $initial_email = $invitedEmail->email;
            
            $relationship = OrgRelationship::query()->where('subject_email = ?', $initial_email)->get();
            if ($relationship)
            {
                $initial_name = $relationship->get_subject_name();
            }
        }
    }


?>

<?php echo view('input/securitytoken'); ?>

<div class='input'>
<label><?php echo __('create:org_name') ?></label><br />
<?php echo view('input/text', array('name' => 'org_name', 'value' => $initial_name, 'trackDirty' => true)) ?>
<div class='help'><?php echo __('create:org_name:help') ?></div>
</div>

<script type='text/javascript'>
function updateUrl()
{
    setTimeout(function()
    {
        var usernameField = document.getElementById('username');
        var username = usernameField.value;
        var urlUsername = document.getElementById('urlUsername');
        urlUsername.removeChild(urlUsername.firstChild);
        urlUsername.appendChild(document.createTextNode(username));
    }, 1);
}
</script>

<div class='input'>
<label><?php echo __('create:username') ?></label><br />
<?php echo view('input/text', array(
    'name' => 'username',
    'id' => 'username',
    'js' => 'onkeyup="javascript:updateUrl()" onchange="javascript:updateUrl()"'
)) ?>
<div class='help' style='font-weight:bold'><?php echo __('create:username:help') ?>
    <span class='websiteUrl'>http://envaya.org/<span id='urlUsername' style='font-weight:bold'><?php echo __('create:username:placeholder') ?></span></span>
</div>
<div style='padding-top:5px' class='help'><?php echo __('create:username:help2') ?></div>
</div>

<div class='input'>
<label><?php echo __('create:password') ?></label><br />
<?php echo view('input/password', array(
    'name' => 'password'
)) ?>
<div class='help'><?php echo __('create:password:help') ?></div>
<div class='help' style='padding-top:5px'><?php echo __('create:password:length') ?></div>
</div>

<div class='input'>
<label><?php echo __('create:password2') ?></label><br />
<?php echo view('input/password', array(
    'name' => 'password2'
)) ?>
</div>

<div class='input'>
<label><?php echo __('create:email') ?></label><br />
<?php echo view('input/email', array(
    'name' => 'email',
    'value' => $initial_email
)) ?>
<div class='help'><?php echo __('create:email:help') ?></div>
<div class='help'><?php echo __('create:email:help_2') ?></div>
</div>


<div class='input'>
<label><?php echo __('create:phone') ?></label><br />
<?php echo view('input/text', array(
    'name' => 'phone',
    'js' => "style='width:200px'"
)) ?>
<div class='help'><?php echo __('create:phone:help') ?></div>
<div class='help'><?php echo __('create:phone:help_2') ?></div>
</div>


<div class='input'>
<label><?php echo __('create:next') ?></label>
<br />
<?php echo view('input/submit',array(
    'value' => __('create:next:button'),
    'trackDirty' => true
));
?>
</div>
