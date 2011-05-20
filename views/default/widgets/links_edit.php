<div class='section_content padded'>
<?php 
    $widget = $vars['widget'];
    $org = $widget->get_root_container_entity();
    
    $query = $org->query_external_sites();    
    $sites = $query->filter();
    
    if (!$sites)
    {
        echo "<p>".__('widget:links:instructions')."</p>";
    }
?>
<script type='text/javascript'>
<?php echo view('widgets/load_link_js', array('widget' => $widget)); ?>

function linkSuccess(res)
{
    function linkConfirmed(include_feed)
    {
        var form = document.forms[1];
        form.url.value = res.url;
        form.site_subtype.value = res.site_subtype;
        form.feed_subtype.value = res.feed_subtype;
        form.feed_url.value = res.feed_url;            
        form.include_feed.value = include_feed ? '1' : '';
        form.action.value = 'add';
        form.submit();    
    }

    if (res.feed_url && !res.has_feed)
    {
        var includeFeedBox;
        var modalBox = createModalBox({
            title: <?php echo json_encode(__('widget:links:options')); ?>, 
            content: createElem('div', {className:'modalBody'},
                createElem('div',
                    createElem('label', {className: 'normalLabel'},
                        includeFeedBox = createElem('input', {
                            type:'checkbox',
                            checked: 'checked',
                            defaultChecked: 'checked',
                            value: '1'
                        }),
                        <?php echo json_encode(__('widget:links:include_feed')); ?>
                    )
                )
            ),
            okFn: function() {
                linkConfirmed(includeFeedBox && includeFeedBox.checked);           
            },
            focus: true
        });
        document.body.appendChild(modalBox);                    
    }
    else
    {
        linkConfirmed(false);
    }
}

function getLinkInfo()
{
    loadLinkInfo($('url').value, linkSuccess);    
}
</script>
<?php
    echo "<form onsubmit='getLinkInfo(); return false;'>";
    echo view('input/securitytoken');
    echo "<table class='inputTable' style='margin:0 auto'>";
    foreach ($sites as $site)
    {
        echo "<tr>";
        echo "<td>";
        echo view_entity($site, array('target' => '_blank'));
        echo "</td>";
        echo "<td>";
        echo view('input/post_link', array(
            'href' => "{$widget->get_edit_url()}?action=remove&guid={$site->guid}",
            'class' => 'gridDelete',
            'text' => '',
        ));
        echo "</td>";
        echo "</tr>";
    }
    echo "<tr>";
    echo "<td style='padding-top:10px'>";
    echo view('input/text', array(
        'id' => 'url',
    ));
    echo "<div id='loading_msg'>&nbsp;</div>";
    echo "</td>";
    echo "<td>";
    echo view('input/submit', array(
        'value' => __('widget:links:add'),
        'js' => "style='margin:0px'"
    ));
    echo "</td>";
    echo "</tr>";
    echo "</table>";
    echo "</form>";
    
    echo view('focus', array('id' => 'url'));   

    ob_start();
    
    echo view('input/hidden', array('name' => 'url'));
    echo view('input/hidden', array('name' => 'title'));
    echo view('input/hidden', array('name' => 'site_subtype'));
    echo view('input/hidden', array('name' => 'feed_subtype'));
    echo view('input/hidden', array('name' => 'feed_url'));
    echo view('input/hidden', array('name' => 'include_feed'));
    echo view('input/hidden', array('name' => 'action'));
    
    $content = ob_get_clean();
    
    echo view("widgets/edit_form", array(
        'widget' => $widget,
        'body' => $content
    ));
        
    if (isset($_GET['url']))
    {
        echo "<script type='text/javascript'>";
        echo "$('url').value = ".json_encode($_GET['url']).";";
        echo "getLinkInfo();";
        echo "</script>";
    }    
?>
</div>