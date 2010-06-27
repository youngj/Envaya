<?php echo elgg_view("org/registerProgress", array('current' => 2)) ?>

<div class='padded'>
<div id='instructions'>
    <?php echo elgg_echo('create:instructions') ?>
</div>

<form action='action/org/register2' method='POST'>

<?php echo elgg_view('input/securitytoken'); ?>

<div class='input'>
<label><?php echo elgg_echo('create:org_name') ?></label><br />
<?php echo elgg_view('input/text', array('internalname' => 'org_name', 'trackDirty' => true)) ?>
<div class='help'><?php echo elgg_echo('create:org_name:help') ?></div>
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
<label><?php echo elgg_echo('create:username') ?></label><br />
<?php echo elgg_view('input/text', array(
    'internalname' => 'username',
    'internalid' => 'username',
    'js' => 'onkeyup="javascript:updateUrl()" onchange="javascript:updateUrl()"'
)) ?>
<div class='help'><?php echo elgg_echo('create:username:help') ?>
    <span class='websiteUrl'>http://envaya.org/<span id='urlUsername' style='font-weight:bold'><?php echo elgg_echo('create:username:placeholder') ?></span></span>
</div>
<div style='padding-top:5px' class='help'><?php echo elgg_echo('create:username:help2') ?></div>
</div>

<div class='input'>
<label><?php echo elgg_echo('create:password') ?></label><br />
<?php echo elgg_view('input/password', array(
    'internalname' => 'password'
)) ?>
<div class='help'><?php echo elgg_echo('create:password:help') ?></div>
<div class='help' style='padding-top:5px'><?php echo elgg_echo('create:password:length') ?></div>
</div>

<div class='input'>
<label><?php echo elgg_echo('create:password2') ?></label><br />
<?php echo elgg_view('input/password', array(
    'internalname' => 'password2'
)) ?>
</div>

<div class='input'>
<label><?php echo elgg_echo('create:email') ?></label><br />
<?php echo elgg_view('input/email', array(
    'internalname' => 'email'
)) ?>
<div class='help'><?php echo elgg_echo('create:email:help') ?></div>
</div>


<div class='input'>
<label><?php echo elgg_echo('create:next') ?></label>
<br />
<?php echo elgg_view('input/submit',array(
    'value' => elgg_echo('create:next:button'),
    'trackDirty' => true
));
?>
</div>


</form>

</div>