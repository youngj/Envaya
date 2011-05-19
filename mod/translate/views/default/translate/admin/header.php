<div id='heading'>
<h1 style='text-align:left'>
<?php
    $sections = array(
        array('url' => '/tr/admin', 'title' => __('itrans:manage'))
    );

    if (isset($vars['items']))
    {
        $items = $vars['items'];
        foreach ($items as $item)    
        {
            $sections[] = array('url' => $item->get_admin_url(), 'title' => $item->get_title());
        }
    }
    
    echo view('breadcrumb', array('items' => $sections));
?>
</h1>
</div>