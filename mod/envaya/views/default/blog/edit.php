<?php

    if (isset($vars['entity'])) 
    {
        $action = "news/edit";
        $body = $vars['entity']->description;
    } 
    else  
    {
        $action = "news/add";
        $container = $vars['container_guid'] ? elgg_view('input/hidden', array('internalname' => 'container_guid', 'value' => $vars['container_guid'])) : "";
    }

    $text_textarea = elgg_view('input/longtext', array('internalname' => 'blogbody', 'value' => $body));

    $submit_input = elgg_view('input/submit', array('internalname' => 'submit', 'value' => elgg_echo('publish')));
    $cat = elgg_echo('categories');
                    
?>

<?php

    if (isset($vars['entity'])) {
      $entity_hidden = elgg_view('input/hidden', array('internalname' => 'blogpost', 'value' => $vars['entity']->getGUID()));
    } else {
      $entity_hidden = '';
    }

$form_body = <<<EOT
        <p class='longtext_editarea'>$text_textarea</p>
        $entity_hidden
        $container
        $submit_input
EOT;

      echo elgg_view('input/form', array('action' => "{$vars['url']}action/$action", 'body' => $form_body, 'internalid' => 'blogPostForm'));
?>

