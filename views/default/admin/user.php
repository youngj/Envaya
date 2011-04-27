<div class='padded'>
<?php
    echo view("admin/user_opt/adduser");
    echo view("admin/user_opt/search");

    if ($vars['list']) echo $vars['list'];

?>
</div>