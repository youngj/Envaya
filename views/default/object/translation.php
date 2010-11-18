<div class='input'>
<?php

$trans = $vars['entity'];

$entity = $trans->get_container_entity();
$prop = $trans->property;

$escUrl = urlencode($_SERVER['REQUEST_URI']);

if ($entity && $entity->can_edit() && $entity->get($prop))
{
    echo view("translation/translate",
        array(
            'entity' => $entity,
            'property' => $prop,
            'targetLang' => $trans->lang,
            'isHTML' => $trans->html,
            'from' => $from));
}
?>
</div>