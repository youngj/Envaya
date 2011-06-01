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
<label><?php echo __('register:org_name') ?></label><br />
<?php echo view('input/text', array('name' => 'org_name', 'value' => $initial_name, 'track_dirty' => true)) ?>
<div class='help'><?php echo __('register:org_name:help') ?></div>
</div>

<div class='input'>
<label><?php echo __('register:username') ?></label><br />
<div class='help'><strong><?php echo __('register:username2') ?></strong></div>
<?php echo view('account/username_field', array('min_length' => 3)); ?>
</div>

<div class='input'>
<label><?php echo __('register:password') ?></label><br />
<?php echo view('input/password', array(
    'name' => 'password'
)) ?>
<div class='help'><?php echo __('register:password:help').' '.__('register:password:remember'); ?></div>
<div class='help' style='padding-top:5px'><?php echo strtr(__('register:password:length'), array('{min}' => 6)); ?></div>
</div>

<div class='input'>
<label><?php echo __('register:password2') ?></label><br />
<?php echo view('input/password', array(
    'name' => 'password2'
)) ?>
</div>

<div class='input'>
<label><?php echo __('register:email') ?></label><br />
<?php echo view('input/email', array(
    'name' => 'email',
    'value' => $initial_email
)) ?>
<div class='help'><?php echo __('register:email:help') ?></div>
<div class='help'><?php echo __('register:email:help_2') ?></div>
</div>


<div class='input'>
<label><?php echo __('register:phone') ?></label><br />
<?php echo view('input/text', array(
    'name' => 'phone',
    'style' => "width:200px"
)) ?>
<div class='help'><?php echo __('register:phone:help') ?></div>
<div class='help'><?php echo __('register:phone:help_2') ?></div>
</div>


<div class='input'>
<label><?php echo __('register:click_to_create') ?></label>
<br />
<?php echo view('input/submit',array(
    'value' => __('register:create_button'),
    'track_dirty' => true
));
?>
</div>

<?php echo view('focus',array(
    'name' => 'org_name'
));
?>