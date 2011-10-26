<div class='section_content padded'>
<?php   
    $query = $vars['query'];
    $limit = 10;
    $offset = (int)get_input('offset');    
    $templates = $query->limit($limit, $offset)->filter();
    $count = $query->count();
    
    $elements = array();
    
    foreach ($templates as $template)
    {
        ob_start();
        
        echo "<div class='email_item' style='padding:3px'>";        
        echo "<div style='float:right'>";        
        echo "<a href='{$template->get_url()}/edit'>".__('edit')."</a> &middot; ";
        echo "<a href='{$template->get_url()}/send'>".__('send')."</a>";
        echo "</div>";                               
                
        echo "<a href='{$template->get_url()}'><strong>".escape($template->get_description())."</strong></a>";

        echo "<br />";
        $filters = $template->get_filters();
        if ($filters)
        {
            echo " [".implode(" + ", array_map(function($filter) { return $filter->render_view(); }, $filters))."]";
        }        
        
        echo " (".get_date_text($template->time_created).")";
        
        echo "</div>";
        
        $elements[] = ob_get_clean();
    }
   
    echo view('paged_list', array(
        'elements' => $elements,
        'count' => $count,
        'offset' => $offset,
        'limit' => $limit,
        'separator' => "<div class='separator'></div>"
    ));

    echo "<br />";
    echo $vars['footer'];
?>
</div>