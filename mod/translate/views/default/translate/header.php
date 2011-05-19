<h2 style='padding:5px'>
<?php
    $sections = array(
        array('url' => '/tr', 'title' => __('itrans:translations'))
    );
    
    if (isset($vars['items']))
    {
        $items = $vars['items'];
        foreach ($items as $item)    
        {
            if (is_array($item))
            {
                $sections[] = $item;
            }
            else
            {
                $sections[] = array(
                    'url' => "{$item->get_url()}", 
                    'title' => $item->get_title()
                );
            }
        }
    }
    
    $title = @$vars['title'];
    if ($title)
    {
        $sections[] = array('title' => $title);
    }
    
    echo view('breadcrumb', array('items' => $sections));
?>
</h2>
<?php
    echo SessionMessages::view_all();
?>