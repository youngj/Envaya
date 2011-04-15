<div class='padded'>
<?php
    echo "<span class=\"contentIntro\">" . view('output/longtext', array('value' => __("admin:user:description"))) . "</span>";

    echo view("admin/user_opt/adduser");

    echo view("admin/user_opt/search");

    if ($vars['list']) echo $vars['list'];

?>
</div>