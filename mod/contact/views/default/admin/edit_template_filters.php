<?php
    $template = $vars['template'];
    $template_class = get_class($template);
    
    $count_filters_url = $template_class::$count_filters_url;
    
    $available_filters = array(
        new Query_Filter_UserType(),
        new Query_Filter_Country(),
        new Query_Filter_Approval(),
        new Query_Filter_Sector(),
    );    
    
    $filters = $displayed_filters = $template->get_filters();

    $filter_map = array();
    foreach ($filters as $filter)
    {
        $filter_map[get_class($filter)] = $filter;
    }
    
    $country_filter = @$filter_map['Query_Filter_Country'];
    if ($country_filter && $country_filter->value)
    {
        $available_filters[] = new Query_Filter_Region(array('country' => $country_filter->value));
    }
        
    foreach ($available_filters as $filter)
    {
        if (!isset($filter_map[get_class($filter)]))
        {
            $displayed_filters[] = $filter;
        }
    }        
?>
<div class='input'>
<label>Filters: </label>

<?php 
    echo "(<span id='filter_count'>";
    echo $template->query_filtered_subscriptions()->count();
    echo "</span>";
    echo "/<span id='total_count'>";
    echo $template_class::query_all_subscriptions()->count(); 
    echo "</span>";
   echo " recipients in filter)";
   
   echo view('js/json');
   echo view('js/xhr');
?>
<script type='text/javascript'>

var displayedFilters = <?php echo Query_Filter::json_encode_filters($displayed_filters); ?>

function updateFiltersJson()
{
    var filters = [];

    for (var i = 0; i < displayedFilters.length; i++)
    {
        var filter = displayedFilters[i];
        if (!filter.args)
        {
            filter.args = {};
        }
    
        var value = filter.args.value = $('filter_' + i).value;
        
        if (value)
        {
            filters.push(filter);
        }
    }
    
    var filtersJson = JSON.stringify(filters);
    
    $('filters_json').value = filtersJson;
        
    fetchJson("<?php echo $count_filters_url; ?>?filters_json=" + encodeURIComponent(filtersJson), function(res) {
        $('filter_count').innerHTML = res.filter_count;
    });
}

</script>
<div id='filter_container' style='padding-left:20px'>
<?php
    echo view('input/hidden', array(
        'id' => 'filters_json', 
        'name' => 'filters_json',
        'value' => Query_Filter::json_encode_filters($filters),
    ));

    foreach ($displayed_filters as $index => $filter)
    {
        echo "<div style='padding-bottom:3px'>";        
        echo escape($filter->get_name()). ": ";
        echo $filter->render_input(array(
            'id' => "filter_$index",
            'onchange' => 'updateFiltersJson()',
        ));
        echo "</div>";
    }
?>
</div>
</div>