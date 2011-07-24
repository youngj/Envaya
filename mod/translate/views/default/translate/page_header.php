<h2 style='padding:5px'>
<?php
    echo view('breadcrumb', $vars);
?>
</h2>
<?php
    echo SessionMessages::view_all();
?>