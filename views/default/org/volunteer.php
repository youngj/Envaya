<div class='section_content padded'>
     <p>Envaya now makes it easier for you to interact with community initiatives in your area.</p>
     
     <p>Below is a list of the organizations on Envaya requesting volunteers.</p>    
     
     <?php
    $user = Session::get_logged_in_user();
    
    if ($user instanceof Organization)
    {
        
        
        if (Widget_Volunteer::query_for_entity($user)->exists())
        {
            $text = "Update your organization's volunteer opportunities!";
        }
        else
        {
            $text = "Add your organization's volunteer opportunities!";
        }
    
        echo "<p><a style='font-weight:bold' href='{$user->get_url()}/page/volunteer/edit'>$text</a></p>";
    }

    $filters = Query_Filter::filters_from_input(array(
        'Query_Filter_Widget_Sector',
        'Query_Filter_Widget_Country',
        'Query_Filter_Widget_Region'
    ));                
    
    echo "<div style='text-align:center'>";
    echo view('org/filter_controls', array(
        'baseurl' => '/pg/volunteer',
        'filters' => $filters,
    ));
    echo "</div>";
    
    $query = Widget_Volunteer::query();    
    $query->where_visible_to_user();    
    $query->apply_filters($filters);
    $query->where_published();
    $query->order_by('time_updated desc');
    
    $limit = 10;
    $offset = (int)get_input('offset');
    $query->limit($limit, $offset);    
        
    $widgets = $query->filter();
    
    $pagination = view('pagination', array(
        'count_displayed' => sizeof($widgets),
        'offset' => $offset,
        'limit' => $limit,
    ));    
    
    if ($widgets)
    {        
        echo "<div style='font-size:12px;padding-top:10px;color:#666'>Note: All volunteer opportunities are submitted and coordinated by the organizations listed below, not by Envaya itself. Envaya does not verify the accuracy of these volunteer opportunities.</div>";        
        
        echo "<table>";
        
        foreach ($widgets as $widget)
        {
            $user = $widget->get_container_user();
            
            if ($user)
            {          
                echo "<tr>";
                echo "<td style='vertical-align:top;padding-top:15px;text-align:center;padding-right:20px'>";
                $link_url = $widget->get_url();
                $thumbnail_url = $widget->thumbnail_url;
                
                if (!$thumbnail_url)
                {
                    $icon_props = $user->get_icon_props('medium');
                    $thumbnail_url = $icon_props['url'];
                }
                
                echo "<div class='blog_date' style='white-space:nowrap'>Posted " .friendly_time($widget->time_updated)."</div>";
                
                echo "<a href='$link_url' style='padding:2px;background-color:#fff;display:inline-block;border:1px solid #ccc'><img src='$thumbnail_url' /></a>";
                
                $content = $widget->render_content(Markup::Feed);
                
                echo "</td>";
                echo "<td  style='vertical-align:top;padding-top:25px'>";
                
                echo "<div class='feed_snippet'>";
                
                echo "<a style='font-weight:bold' href='$link_url'>".escape($user->name)."</a>";
                echo " - ";
                
                $max_length = 350;
                
                echo Markup::get_snippet($content, $max_length);

                if (strlen($content) > $max_length)
                {
                    echo " <a class='feed_more' href='$link_url'>".__('feed:more')."</a>";
                }
                
                echo "</div>";
                echo "</td>";
            }
        }            
        
        echo "</table>";        
        echo $pagination;        
    }
    else
    {
        echo "<div style='padding-top:10px'>No volunteer opportunities found.</div>";
        echo $pagination;
    }    
        
?>
</div>