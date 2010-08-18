<div class='input'>
<?php

$trans = $vars['entity'];

$entity = $trans->getContainerEntity();

$prop = $trans->property;

$org = $trans->getRootContainerEntity();

$escUrl = urlencode($_SERVER['REQUEST_URI']);

echo "<a style='float:right' href='org/translate?from=$escUrl&prop[]={$entity->guid}.{$prop}'>".__('trans:contribute')."</a>";
echo "<a href='{$org->getURL()}'>".escape($org->name)."</a> : ";
echo "<a href='{$entity->getURL()}'>".escape($entity->getTitle())."</a>";

echo "<div>".Markup::get_snippet($entity->$prop)."</div>";

?>
</div>