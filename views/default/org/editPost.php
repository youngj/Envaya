
<?php
    $blog = $vars['entity'];    

    if ($blog) 
    {
        $action = "news/edit";
        $body = $vars['entity']->content;
    } 
    else  
    {
        $action = "news/add";
        $body = '';
    }

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
    if (isset($vars['entity'])) 
    {
        echo elgg_view('input/hidden', array('internalname' => 'blogpost', 'value' => $vars['entity']->getGUID()));
    } 
    else
    {
        echo elgg_view('input/hidden', array('internalname' => 'container_guid', 'value' => $vars['container_guid']));
    }
?>    


<?php echo elgg_view('input/submit', array('internalname' => 'submit', 'value' => elgg_echo('publish'))); ?>
<?php
    $form_body = ob_get_clean();

    echo elgg_view('input/form', array('action' => "{$vars['url']}action/$action", 'enctype' => "multipart/form-data", 'body' => $form_body, 'internalid' => 'blogPostForm'));
?>

