<?php echo elgg_view("org/registerProgress", array('current' => 2)) ?>

<div class='padded'>
<div id='instructions'>
    <?php echo __('create:instructions') ?>
</div>

<form action='org/register2' method='POST'>

<?php echo elgg_view('input/securitytoken'); ?>

<div class='input'>
<label><?php echo __('create:org_name') ?></label><br />
<?php echo elgg_view('input/text', array('internalname' => 'org_name', 'trackDirty' => true)) ?>
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
<?php echo elgg_view('input/text', array(
    'internalname' => 'username',
    'internalid' => 'username',
    'js' => 'onkeyup="javascript:updateUrl()" onchange="javascript:updateUrl()"'
)) ?>
<div class='help' style='font-weight:bold'><?php echo __('create:username:help') ?>
    <span class='websiteUrl'>http://envaya.org/<span id='urlUsername' style='font-weight:bold'><?php echo __('create:username:placeholder') ?></span></span>
</div>
<div style='padding-top:5px' class='help'><?php echo __('create:username:help2') ?></div>
</div>

<div class='input'>
<label><?php echo __('create:password') ?></label><br />
<?php echo elgg_view('input/password', array(
    'internalname' => 'password'
)) ?>
<div class='help'><?php echo __('create:password:help') ?></div>
<div class='help' style='padding-top:5px'><?php echo __('create:password:length') ?></div>
</div>

<div class='input'>
<label><?php echo __('create:password2') ?></label><br />
<?php echo elgg_view('input/password', array(
    'internalname' => 'password2'
)) ?>
</div>

<div class='input'>
<label><?php echo __('create:email') ?></label><br />
<?php echo elgg_view('input/email', array(
    'internalname' => 'email'
)) ?>
<div class='help'><?php echo __('create:email:help') ?></div>
<div class='help'><?php echo __('create:email:help_2') ?></div>
</div>


<div class='input'>
<label><?php echo __('create:phone') ?></label><br />
<?php echo elgg_view('input/text', array(
    'internalname' => 'phone',
    'js' => "style='width:200px'"
)) ?>
<div class='help'><?php echo __('create:phone:help') ?></div>
<div class='help'><?php echo __('create:phone:help_2') ?></div>
</div>


<div class='input'>
<label><?php echo __('create:next') ?></label>
<br />
<?php echo elgg_view('input/submit',array(
    'value' => __('create:next:button'),
    'trackDirty' => true
));
?>
</div>


</form>

</div>