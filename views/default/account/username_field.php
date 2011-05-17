<script type='text/javascript'>
<?php echo view('js/dom'); ?>
function updateUrl()
{
    setTimeout(function()
    {
        var usernameField = $('username');
        var username = usernameField.value;
        var urlUsername = $('urlUsername');
        removeChildren(urlUsername)
        urlUsername.appendChild(document.createTextNode(username));
    }, 1);
}
</script>

<?php echo view('input/text', array(
    'name' => 'username',
    'id' => 'username',
    'value' => @$vars['value'],
    'js' => 'onkeyup="javascript:updateUrl()" onchange="javascript:updateUrl()"'
)) ?>
<div class='help' style='font-weight:bold'><?php echo __('register:username:help') ?>
    <span class='websiteUrl'>http://envaya.org/<span id='urlUsername' style='font-weight:bold'><?php 
        echo @$vars['value'] ?: __('register:username:placeholder');
    ?></span></span>
</div>
<div style='padding-top:5px' class='help'><?php echo strtr(__('register:username:help2'), array('{min}' => $vars['min_length'])); ?></div>