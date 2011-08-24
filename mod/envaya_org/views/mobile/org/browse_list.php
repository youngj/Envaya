<?php 

$filters = Query_Filter::filters_from_input(array('Sector','Country','Region'));

echo view('org/current_filter', array(
    'filters' => $filters, 
    'changeurl' => '/pg/change_browse_view'
));
?>

<div class='padded'>
<?php
$res = view('org/search_list', array(    
    'filters' => $filters,
));

if ($res)
{
    echo $res;
}
else
{
    echo __("search:noresults");
}

?>
</div>