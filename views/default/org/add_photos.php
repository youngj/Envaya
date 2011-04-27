<?php
    $org = $vars['org'];
?>
<div class='section_content padded'>
<form method='POST' enctype='multipart/form-data' action='<?php echo $org->get_url() ?>/addphotos'>
<?php
    echo view('input/securitytoken');
    echo view('input/uniqid');        
    echo view('org/add_photos_content');
?>

</form>
</div>