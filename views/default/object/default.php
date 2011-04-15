<?php
    $entity = $vars['entity'];

    $title = $entity->title ?: $entity->name ?: get_class($entity);
    
    $info = "<div><p><b><a href='{$entity->get_url()}'>".escape($title)."</a></b></p></div>";

    echo view('search/listing',array('info' => $info));
