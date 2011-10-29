<?php
    $user = $vars['user'];
?>
<div class='section_content padded'>
<form method='POST' enctype='multipart/form-data' action='<?php echo $user->get_url() ?>/addphotos'>
<?php
    echo view('input/securitytoken');
    echo view('input/uniqid');        
    echo view('account/add_photos_content');
?>

</form>
</div>