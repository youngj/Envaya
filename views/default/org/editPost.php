<?php
    $blog = $vars['entity'];

    $body = $vars['entity']->content;

    ob_start();
?>
<div class='input'>
    <label><?php echo elgg_echo('blog:content:label') ?></label>
    <?php echo elgg_view('input/tinymce', array(
        'internalname' => 'blogbody',
        'trackDirty' => true,
        'valueIsHTML' => $blog->hasDataType(DataType::HTML),
        'value' => $body)) ?>
</div>


<?php
    echo elgg_view('input/hidden', array('internalname' => 'blogpost', 'value' => $vars['entity']->getGUID()));
    echo elgg_view('input/alt_submit', array(
            'internalname' => "delete",
            'internalid' => 'widget_delete',
            'trackDirty' => true,
            'confirmMessage' => elgg_echo('blog:delete:confirm'),
            'value' => elgg_echo('blog:delete')
        ));

    echo elgg_view('input/submit', array('internalname' => 'submit', 'trackDirty' => true, 'value' => elgg_echo('savechanges')));
?>

<?php
    $form_body = ob_get_clean();
    echo elgg_view('input/form', array('action' => "action/org/editPost", 'enctype' => "multipart/form-data", 'body' => $form_body));
?>