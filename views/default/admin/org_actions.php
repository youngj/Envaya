<?php
    $org = $vars['org'];
?>
<div class='adminBox'>
<?php

echo view('admin/org_actions_items', array('org' => $org));
echo implode(' ', PageContext::get_submenu('org_actions')->render_items()); 

?>
</div>