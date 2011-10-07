<div class='padded'>
<?php

PageContext::add_header_html('<meta name="robots" content="noindex,follow" />'); 

$filters = $vars['filters'];
$items = $vars['items'];
$first_id = (int)$vars['first_id'];

echo view('org/filter_controls', array(
    'baseurl' => '/pg/feed',
    'filters' => $filters,
));
?>
</div>
<div id='feed_container'>
<?php	
	echo view('feed/list', array('items' => $items, 'show_edit_controls' => Session::isadminloggedin()));
?>
<div class='separator'></div>
</div>

<?php 
if ($first_id)
{
?>

<script type='text/javascript'>
<?php
    echo view('js/xhr');
    echo view('js/dom');
?>
var fetchMoreXHR = null;
var first_id = <?php echo json_encode($first_id); ?>;

function loadMore()
{
    if (first_id)
    {
        var link = $('load_more_link');
        if (link.blur)
        {
            link.blur();
        }
        link.style.display = 'none';
        $('load_more_progress').style.display = 'block';    
    
        var $src = "/pg/feed_more?<?php
            foreach ($filters as $filter)
            {
                echo $filter->get_param_name()."=".urlencode($filter->value)."&";
            }   
        ?>before_id=" + first_id;

        if (fetchMoreXHR)
        {
            fetchMoreXHR.abort();
            fetchMoreXHR = null;
        }
        fetchMoreXHR = fetchJson($src, itemsLoaded);
    }
}
function itemsLoaded(res)
{
    var container = $('feed_container');
    var childContainer = createElem('div');
    childContainer.innerHTML = res.items_html + "<div class='separator'></div>";
    container.appendChild(childContainer);
    first_id = res.first_id;
        
    $('load_more_link').style.display = 'inline';
    $('load_more_progress').style.display = 'none';
    
    if (!res.first_id)
    {
        $('load_more').style.display = 'none';
    }
}
</script>
<div id='load_more'>
<a id='load_more_link' href='javascript:loadMore()'><?php echo __('feed:show_more'); ?></a>
<div id='load_more_progress' style='display:none'><?php echo __('loading'); ?></div>
</div>
<?php 
 }
?>