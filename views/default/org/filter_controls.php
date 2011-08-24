<?php 
    $baseurl = $vars['baseurl'];               
    $filters = $vars['filters'];
    
    $filters_map = array();
    $select_elems = array();
    
    foreach ($filters as $filter)
    {
        if ($filter->is_valid())
        {
            $name = $filter->get_param_name();
            $select_elems[] = $filter->render_input(array(
                'name' => $name,
                'id' => "filter_$name",
                'style' => 'margin-bottom:5px',
                'onchange' => 'filterChanged()',
            ));
            $filters_map[$name] = $filter;
        }
    }        
?>
<form method='GET' action='<?php echo escape($baseurl); ?>'>
<script type='text/javascript'>
function filterChanged()
{
    setTimeout(function() {        
        
        <?php 
        $js_exp = array();
        foreach ($filters_map as $name => $filter)
        {
            $js_exp[] = "'$name='+$('filter_{$name}').value";
        }
        ?>        
        
        var baseUrl = <?php echo json_encode($baseurl); ?>,
            connector = (baseUrl.indexOf("?") == -1) ? "?" : "&";
        
        var newUrl = baseUrl + connector + <?php echo implode("+'&'+", $js_exp); ?>;
        
        <?php
        if (@$filters_map['region'] && @$filters_map['country'])
        {   
        ?>              
            var newCountry = $('filter_country').value;
            var oldCountry = <?php echo json_encode(get_input('country')); ?>;

            if (!newCountry || newCountry != oldCountry)
            {
                newUrl = urlWithParam(newUrl, 'region', '');
            }
        <?php
        }
        ?>        
        location.href = newUrl;
    }, 1);
}
</script>
<?php echo implode(' ', $select_elems); ?>
<noscript>
<?php echo view('input/submit', array('value' => __('go'))); ?>
</noscript>
</form>
