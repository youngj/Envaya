<?php
    $email = @$vars['email'];
    
    if (!$email)
    {
        $email = new EmailTemplate();
        $email->set_filters(array(
            new Query_Filter_UserType(array('value' => Organization::get_subtype_id())),
            new Query_Filter_Approval(array('value' => User::Approved)),
        ));
    }
    
    $available_filters = array('UserType','Country','Approval','Sector');    
?>
<div class='input'>
<label>Filters: </label>(<span id='filter_count'><?php 
    echo $email->query_filtered_subscriptions()->count();
?></span>/<span id='total_count'><?php 
    echo EmailTemplate::query_all_subscriptions()->count(); 
?></span> recipients in filter)
<div id='filter_container'></div>
<div>Add filter: 
<span style='font-weight:bold'>
<?php    
    $add_links = array();
    foreach ($available_filters as $available_filter)
    {
        $cls = "Query_Filter_{$available_filter}";
        $add_links[] = "<a id='add_filter_{$available_filter}' href='javascript:addFilter(\"$available_filter\")'>".escape($cls::get_name())."</a> ";
    }
    echo implode(" &middot; ", $add_links);
?>
</span>
<?php echo view('input/hidden', array('id' => 'filters_json', 'name' => 'filters_json', 'value' => $email->filters_json)) ?>
</div>    
<script type='text/javascript'>
<?php echo view('js/dom'); ?>
<?php echo view('js/json'); ?>
<?php echo view('js/xhr'); ?>

var filterIndex = 0;

function getNewFilterId()
{
    return "filter" + (filterIndex++);
}

function addFilterInput(id, subclass, name, inputHTML)
{
    showAddFilter(subclass, false);

    var div = createElem('div', {
            id: id,
        },
        createElem('input', {type:'hidden', id:id + '_subclass', value:subclass}),
        createElem('span', name + ": "),
        createElem('span', {innerHTML: inputHTML}),
        " ",
        createElem('a', {
            href: 'javascript:void(0)', 
            click: function() { 
                showAddFilter(subclass, true);
                removeElem(div);
                updateFiltersJson();
            }
        }, "X")
    );        
    div.style.paddingBottom = '3px';
    div.style.paddingLeft = '60px';
        
    function addChangeEvent(elem)
    {
        addEvent(elem,'change',updateFiltersJson);
    }
    
    each(div.getElementsByTagName('select'), addChangeEvent);
    each(div.getElementsByTagName('input'), addChangeEvent);    
    
    $('filter_container').appendChild(div);    
    
    updateFiltersJson();
}

function getFilter(div)
{
    var filter = {subclass:'', args:{}};
    var id = div.id;   
    
    filter.subclass = $(id + '_subclass').value;
    filter.args.value = $(id + '_value').value;    
    
    return filter;
}

function updateFiltersJson()
{
    var prev = $('filters_json').value;
    var filtersJson = getFiltersJson();
        
    $('filters_json').value = filtersJson;
    
    if (prev != filtersJson)
    {
        $('filter_count').innerHTML = "?";
        fetchJson("/admin/contact/filters_count?filters_json=" + encodeURIComponent(filtersJson), function(res) {            
            $('filter_count').innerHTML = res.filter_count;
        });
    }
}

function getFiltersJson()
{
    var filters = each($('filter_container').childNodes, getFilter);
    return JSON.stringify(filters);
}

function showAddFilter(subclass, show)
{
    var elem = $('add_filter_' + subclass);
    
    if (elem)
    {
        elem.style.display = show ? 'inline' : 'none';
    }
}

function addFilter(subclass)
{
    var id = getNewFilterId();

    fetchJson("/admin/contact/filter_input?id="+id+"_value&subclass=" + subclass, function(res) {            
        addFilterInput(id, subclass, res.name, res.input_html);
    });
}    

<?php 
    $i = 0;    
    foreach ($email->get_filters() as $filter) 
    { 
        $filter_id = "filterx{$i}";
        echo "addFilterInput('$filter_id',".json_encode($filter->get_subclass()).","
            .json_encode($filter->get_name()).","
            .json_encode($filter->render_input(array(
                'empty_option' => false,
                'id' => "{$filter_id}_value"
            ))).");";
        $i++;
    } 
        
?>
</script>
</div>