<?php

    $country = $vars['country'];

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
            
            $relationship = Relationship::query()->where('subject_email = ?', $initial_email)->get();
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
<?php echo view('input/text', array('id' => 'name', 'name' => 'org_name', 'value' => $initial_name, 'track_dirty' => true)) ?>
<div class='help'><?php echo __('register:org_name:help') ?></div>
</div>

<div class='input' style='padding-bottom:12px'>
<label><?php echo __('register:location') ?></label>
<table class='inputTable'>
<tr>
<th>
<?php echo __('register:city') ?> 
</th>
<td style='vertical-align:middle'>
<?php echo view('input/text', array(
    'id' => 'city',
    'name' => 'city',
    'style' => 'width:200px',
    'value' => '',
)); ?>, <?php echo escape(Geography::get_country_name($country)); ?>
</td>
</tr>
<tr>
<th>
<?php echo __('register:region') ?> 
</th>
<td>
<?php echo view('input/pulldown', array(
    'id' => 'region',
    'name' => 'region',
    'options' => Geography::get_region_options($country),
    'empty_option' => __('register:region:blank'),
)) ?>
</td>
</tr>
</table>
</div>
<div class='input'>
<label><?php echo __('register:username') ?></label><br />
<div class='help'><strong><?php echo __('register:username2') ?></strong></div>
<?php echo view('account/username_field', array('min_length' => 3)); ?>
</div>

<div class='input'>
<label><?php echo __('register:password') ?></label><br />
<?php echo view('input/password', array(
    'id' => 'password',
    'name' => 'password'
));
    echo view('js/password_strength');
 ?>
<div id='password_strength' style='height:2px;margin-left:4px;overflow:hidden'></div>
<script type='text/javascript'>
function updatePasswordStrength()
{
    var region = $('region'), regionName = '';
    if (region.value)
    {
        regionName = region.options[region.selectedIndex].text;
        console.log(regionName);
    }

    setTimeout(function() {
        PasswordStrength.show(
            $('password').value, 
            [
                $('name').value, $('username').value, $('email').value, $('phone').value, 
                $('city').value, <?php echo json_encode(Geography::get_country_name($country) ?: ''); ?>,
                regionName
            ],
            PasswordStrength.VeryWeak,
            $('password_strength')
        );
    }, 10);
}
addEvent($('password'), 'keypress', updatePasswordStrength);
addEvent($('password'), 'change', updatePasswordStrength);
</script>
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
    'id' => 'email',
    'name' => 'email',
    'value' => $initial_email
)) ?>
<div class='help'><?php echo __('register:email:help') ?></div>
<div class='help'><?php echo __('register:email:help_2') ?></div>
</div>


<div class='input'>
<label><?php echo __('register:phone') ?></label><br />
<?php echo view('input/text', array(
    'id' => 'phone',
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