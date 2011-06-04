<div class='section_content padded'>

<?php echo $vars['widget']->render_content(); ?>

<form method="POST" action='/org/send_feedback'>
<?php echo view('page/contact_form') ?>
</form>
</div>