<div class='section_content padded'>
<?php
    $blog = $vars['entity'];

    $body = $vars['entity']->content;

    ob_start();
?>
<div class='input'>
    <?php echo view('input/tinymce', array(
        'name' => 'blogbody',
        'autoFocus' => true,
        'trackDirty' => true,
        'value' => $body)) ?>
</div>


<?php
    echo view('input/alt_submit', array(
            'name' => "delete",
            'id' => 'widget_delete',
            'trackDirty' => true,
            'confirmMessage' => __('blog:delete:confirm'),
            'value' => __('blog:delete')
        ));

    echo view('input/submit', array('value' => __('savechanges')));
?>

<?php
    $form_body = ob_get_clean();
    echo view('input/form', array('action' => "{$vars['entity']->get_url()}/edit", 'enctype' => "multipart/form-data", 'body' => $form_body));
?>
</div>