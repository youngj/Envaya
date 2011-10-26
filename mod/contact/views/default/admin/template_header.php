<div id='heading'>
<h1>
<?php
    $items = array(
        array('url' => $vars['index_url'], 'title' => $vars['index_title'])
    );
    
    $template = @$vars['template'];
    if ($template)
    {
        $items[] = array(
            'url' => $template->get_url(), 
            'title' => $template->get_description()
        );
    }
    
    $title = @$vars['title'];
    if ($title)
    {
        $items[] = array('title' => $title);
    }
    
    echo view('breadcrumb', array('items' => $items));
?>
</h1>
</div>