<?php
    $blog = $vars['entity'];    

    $body = $vars['entity']->content;

    ob_start();                
?>
<div class='input'>
    <label><?php echo elgg_echo('blog:content:label') ?></label>
    <?php echo elgg_view('input/longtext', array('internalname' => 'blogbody', 'value' => $body)) ?>
</div>

<div class='input'>
<label><?php echo elgg_echo('blog:image:label') ?></label><br />
<?php echo elgg_view('input/image', array(
        'current' => ($blog && $blog->hasImage() ? $blog->getImageUrl('small') : null),
        'internalname' => 'image',
        'deletename' => 'deleteimage',
    )) ?>    
</div>

<?php
    echo elgg_view('input/hidden', array('internalname' => 'blogpost', 'value' => $vars['entity']->getGUID()));
    echo elgg_view('input/submit', array('internalname' => 'submit', 'value' => elgg_echo('blog:save'))); 
    echo elgg_view('input/submit', array(
            'internalname' => "delete", 
            'internalid' => 'widget_delete', 
            'js' => "onclick='return confirm(".json_encode(elgg_echo('question:areyousure')).")'",
            'value' => elgg_echo('blog:delete')
        )); 
?>

<?php
    $form_body = ob_get_clean();
    echo elgg_view('input/form', array('action' => "action/news/edit", 'enctype' => "multipart/form-data", 'body' => $form_body));
?>