<?php
    $blog = $vars['entity'];

    $body = $vars['entity']->content;

    ob_start();
?>
<div class='input'>
    <?php echo view('input/tinymce', array(
        'internalname' => 'blogbody',
        'trackDirty' => true,
        'valueIsHTML' => $blog->has_data_type(DataType::HTML),
        'value' => $body)) ?>
</div>


<?php
    echo view('input/alt_submit', array(
            'internalname' => "delete",
            'internalid' => 'widget_delete',
            'trackDirty' => true,
            'confirmMessage' => __('blog:delete:confirm'),
            'value' => __('blog:delete')
        ));

    echo view('input/submit', array('internalname' => 'submit', 'trackDirty' => true, 'value' => __('savechanges')));
?>

<?php
    $form_body = ob_get_clean();
    echo view('input/form', array('action' => "{$vars['entity']->get_url()}/save", 'enctype' => "multipart/form-data", 'body' => $form_body));
?>