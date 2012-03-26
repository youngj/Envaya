<?php
    /*
     * Pagination control. Displays previous/next links, and a list of links to numbered pages.
     */

    $offset = 0;                        // 0-based offset of first item on current page (in items, not pages)
    $limit = 10;                        // maximum number of items per page
    $count = null;                      // total number of items in data source (null means unknown)
    $count_displayed = 0;               // number of items displayed on this page (only needed if count is null)
    $pages_shown = 12;                  // maximum number of links to pages shown in pagination control 
    $word = 'offset';                   // url parameter used to link to a page with a different offset
    $baseurl = $_SERVER['REQUEST_URI']; // base URL of links in pagination control
    $force_next = false;                // always show next link
    $id_prefix = "page{$INCLUDE_COUNT}_";
    extract($vars);
    
    $offset = (int)$offset;
    $limit = (int)$limit;
    
    $count_unknown = ($count === null);
    $count_min = ($count_unknown) ? ($offset + $count_displayed) : $count;               
        
    // only display if there is content to paginate through,
    // or if we already have an offset, or if we don't know the total count
    if ($count > $limit || $offset > 0 || $count_unknown) {
?>
<div class="pagination">
<?php

    if ($offset > 0) 
    {
        $prev_offset = max($offset - $limit, 0);
        $prev_url = url_with_param($baseurl, $word, $prev_offset);        
        echo "<a href=\"{$prev_url}\" class=\"pagination_previous\">&laquo; ". __("previous") ."</a> \n";
    }

    if ($offset > 0 || $count_min > $limit) 
    {
        $pages_edge = ceil($pages_shown / 3);
        $pages_center = ceil($pages_shown / 6);

        $current_page = floor($offset / $limit) + 1; // 1-based page number
        
        $all_pages = ceil($count_min / $limit);

        $pages_array = array();
        
        // get page numbers at the beginning
        for ($i = 1; 
            $i <= $all_pages && $i <= $pages_edge; 
            $i++) 
        {
            $pages_array[] = $i;
        }
        
        // get page numbers surrounding current page
        for ($i = max($i, $current_page - $pages_center); 
            $i <= $all_pages && $i <= ($current_page + $pages_center); 
            $i++) 
        {
            $pages_array[] = $i;
        }
        
        // get page numbers at end
        for ($i = max($i, $all_pages - ($pages_edge - 1)); 
            $i <= $all_pages; 
            $i++) 
        {
            $pages_array[] = $i;
        }

        // output page numbers with links
        $prev = 0;
        foreach ($pages_array as $i) 
        {
            if (($i - $prev) > 1) 
            {
                echo "<span class=\"pagination_more\">...</span>\n";
            }

            $cur_offset = (($i - 1) * $limit);
            $count_url = url_with_param($baseurl, $word, $cur_offset);

            if ($cur_offset != $offset) 
            {
                echo " <a href=\"{$count_url}\" class=\"pagination_number\">{$i}</a> \n";
            } 
            else 
            {
                echo "<span class=\"pagination_currentpage\"> {$i} </span>\n";
            }
            $prev = $i;
        }
    }

    $next_offset = $offset + $limit;
    if ($force_next || $next_offset < $count_min || ($count_unknown && $next_offset <= $count_min)) 
    {
        $next_url = url_with_param($baseurl, $word, $next_offset);
        echo " <a id='{$id_prefix}next' href=\"{$next_url}\" class=\"pagination_next\">" . __("next") . " &raquo;</a>\n";
    }

?>
<div style="clear:both"></div>
</div>
<?php
    } // end of pagination check if statement
?>