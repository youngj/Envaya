<div id='heading'>
<h1>
<?php
    $entity = $vars['entity'];
    $breadcrumb_items = array();
    $cur = $entity;
    while ($cur)
    {
        $breadcrumb_items[] = array(
            'url' => $cur->get_admin_url(),
            'title' => $cur->get_title()
        );
        $cur = $cur->get_container_entity();
    }
    echo view('breadcrumb', array('items' => array_reverse($breadcrumb_items)));
?>
</h1>
</div>