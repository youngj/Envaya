<?php 

$filters = Query_Filter::filters_from_input(array('Sector','Country','Region'));

?>
<div class='section_content padded'>
<div class='view_toggle' style='padding-left:30px'>
    <a href='/pg/browse?list=0&sector=<?php echo escape($filters[0]->value); ?>'><?php echo __('browse:map') ?></a> &middot;
    <strong><?php echo __('browse:list') ?></strong>
</div>
<?php
    echo view('org/filter_controls', array(
        'baseurl' => '/pg/browse?list=1', 
        'filters' => $filters
    ));
    echo "<br />";

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