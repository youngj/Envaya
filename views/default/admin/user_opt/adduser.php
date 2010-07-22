<?php
	/**
	 * Add a user.
	 * Form to add a new user.
	 * 
	 * @package Elgg
	 * @subpackage Core
	 * @author Curverider Ltd
	 * @link http://elgg.org/
	 */
?>

<script type='text/javascript'>
function toggle($id)
{
    var elem = document.getElementById($id);
    if (elem.style.display == 'block')
    {
        elem.style.display = 'none';
    }
    else
    {
        elem.style.display = 'block';
    }
}
</script>

<div class="admin_adduser_link">
    <a href="javascript:toggle('add_user_showhide')"><?php echo __('admin:user:adduser:label'); ?></a>
</div>
<div id="add_user_showhide" style="display:none" >
<?php echo elgg_view('account/forms/useradd', array('show_admin'=>true)); ?>
</div>