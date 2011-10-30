<?php
    $user = $vars['user'];
?>
<div class='adminBox'>
<?php

echo view('admin/user_actions_items', array('user' => $user));
echo implode(' ', PageContext::get_submenu('user_actions')->render_items()); 

?>
</div>