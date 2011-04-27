<h2 style='padding:5px'>
<?php
    $sections = array(
        array('url' => '/tr', 'title' => __('itrans:title'))
    );

    $items = @$vars['items'];
    foreach ($items as $item)    
    {
        $sections[] = array('url' => $item->get_url(), 'title' => $item->get_title());
    }
    
    echo view('breadcrumb', array('items' => $sections));
?>
</h2>