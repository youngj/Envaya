<?php 
    $sector = get_input('sector');     
    $region = get_input('region');
    $country = get_input('country');
?>
<script type='text/javascript'>

function selectOrg(guid, selected)
{
    var link = $('org_'+guid);
    link.style.color = selected ? '#333' : '';
}

function selectIfRecipient(guid, email)
{
    selectOrg(guid, parent.isRecipient(email));
}

function toggleRecipient(guid, email)
{   
    if (!parent.isRecipient(email))
    {
        selectOrg(guid, true);
        parent.addRecipient(email);
    }
    else
    {
        selectOrg(guid, false);
        parent.removeRecipient(email);
    }
}
</script>

<div class='padded'>
<?php
    echo "<div style='padding-bottom:5px'>";
    echo __('share:browse_instructions');
    echo "</div>";
    echo view('org/filter_controls', array('baseurl' => '/pg/browse_email'));

    $query = Organization::query()->where_visible_to_user()->where("email <> ''");    

    $query->with_sector($sector);
    $query->with_region($region);
    $query->with_country($country);
    
    $query->order_by('name');                
    
    $limit = 10;
    $offset = (int)get_input('offset');
        
    $orgs = $query->limit($limit, $offset)->filter();
    $count = $query->count();
    
    if ($count)
    {
        echo view('pagination', array(
            'offset' => $offset,
            'limit' => $limit,
            'count' => $count,
        ));    
    
        echo "<ul>";
        foreach ($orgs as $org)
        {
            echo "<li>";
            echo "<a id='org_{$org->guid}' title='".escape($org->email)."' href='javascript:void(0)' 
                onclick='toggleRecipient($org->guid,".json_encode($org->email).");'>";
            echo "<span style='font-weight:bold'>".escape($org->name)."</span>";
            echo "</a>";
            echo "<script type='text/javascript'>";
            echo "selectIfRecipient($org->guid,".json_encode($org->email).");";            
            echo "</script>";
            echo "</li>";
        }
        echo "</ul>";    
    }
    else
    {
        echo __("search:noresults");
    }

?>
</div>