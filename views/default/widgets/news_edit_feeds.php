<?php
    $widget = $vars['widget'];

    $feeds = $widget->query_external_feeds()->filter();       
?>
<script type='text/javascript'>
<?php echo view('widgets/load_link_js', array('widget' => $widget)); ?>

function removeFeed(guid, url, hasItems)
{
    function removeConfirmed(remove_posts)
    {
        setSubmitted(true);    
        var form = $('feed_remove_form');
        form.guid.value = guid;        
        form.remove_posts.value = remove_posts ? '1' : '';
        setTimeout(function() { form.submit() }, 1);
    }

    var modalBox;

    if (!hasItems)
    {
        // no need to show confirmation dialog if there are no items from this feed
        removeConfirmed(false);
        return;
    }
    var modalBox = createModalBox({
        title: <?php echo json_encode(__('widget:news:feeds:confirm_remove_title')); ?>,
        hideOk: true,
        focus: true,
        width:460,
        content: createElem('div', {className:'modalBody'},
            createElem('p', <?php echo json_encode(__('widget:news:feeds:confirm_remove')); ?>.replace('{url}',url)),
            createElem('div', {className:'padded'},
                createElem('strong',
                    createElem('a', {
                            href: 'javascript:void(0)',
                            click: function(){ removeConfirmed(true);}
                        },
                        <?php echo json_encode(__('widget:news:feeds:remove_posts')); ?>.replace('{url}',url)
                    )
                )
            ),
            createElem('div', {className:'padded'},
                createElem('strong',
                    createElem('a', {
                            href: 'javascript:void(0)',
                            click: function(){ removeConfirmed(false);}
                        },
                        <?php echo json_encode(__('widget:news:feeds:keep_posts')); ?>.replace('{url}',url)
                    )
                )
            )
        )
    });    
    document.body.appendChild(modalBox);                    
}

function linkSuccess(res)
{
    if (!res.feed_url)
    {
        var urlDiv = createElem('div', res.url);
        var escUrl = urlDiv.innerHTML;
    
        var modalBox = createModalBox({
            title: <?php echo json_encode(__('widget:links:options')); ?>, 
            content: createElem('div', {className:'modalBody'},
                createElem('p', <?php echo json_encode(__('widget:news:feeds:error')); ?>),
                createElem('div', {innerHTML: <?php echo json_encode(__('widget:news:feeds:add_link')); ?>.replace('{url}', 
                    '<a href="'+escUrl+'" style="white-space:nowrap" target="_blank">'+escUrl+'</a>'
                )})
            ),
            okFn: function() {
                var form = $('feed_add_link_form');
                form.url.value = res.url;
                setTimeout(function() { form.submit(); }, 1);
            },
            focus: true
        });
    }    
    else
    {    
        function addConfirmed(add_link)
        {
            var form = $('feed_add_form');
            form.url.value = res.url;
            form.feed_url.value = res.feed_url;
            form.feed_subtype.value = res.feed_subtype;
            form.add_link.value = add_link ? '1' : '';
            setTimeout(function() { form.submit(); }, 1);
        }    
    
        var addLinkBox = null;         
        
        var titleField = createElem('input', {
            type:'text', 
            className:'input-text', 
            value: res.url
        });    
        
        var modalBox = createModalBox({
            title: <?php echo json_encode(__('widget:links:options')); ?>, 
            content: createElem('div', {className:'modalBody'},
                createElem('div',
                    createElem('label', {className: 'normalLabel'},
                        addLinkBox = createElem('input', {
                            type:'checkbox',
                            checked: 'checked',
                            defaultChecked: 'checked',
                            value: '1'
                        }),
                        <?php echo json_encode(__('widget:news:feeds:include_link')); ?>
                    )
                )                
            ), 
            okFn: function() {
                addConfirmed(addLinkBox.checked);            
            },
            focus: true
        });
    }
    document.body.appendChild(modalBox);                    
}

function getLinkInfo()
{
    loadLinkInfo($('feed_url').value, linkSuccess);    
}
</script>
<?php    
    ob_start();
    
    if (!$feeds)
    {
        echo "<p>".__('widget:news:feeds:instructions')."</p>";
    }
    
    echo "<form id='feed_form' onsubmit='getLinkInfo(); return false;'>";
    echo view('input/securitytoken');    
    echo "<table class='inputTable' style='margin:0 auto'>";
    foreach ($feeds as $feed)
    {
        $has_items = $widget->query_widgets()->with_metadata('feed_guid', $feed->guid)->exists();
    
        echo "<tr>";
        echo "<td>";
        echo "<a target='_blank' href='".escape($feed->url)."' style='font-weight:bold'>".escape($feed->url)."</a>";
        echo "</td>";
        echo "<td>";
        echo "<a href='javascript:removeFeed($feed->guid, ".json_encode($feed->url).",".json_encode($has_items).");' class='gridDelete'></a>";
        echo "</td>";
        echo "</tr>";
    }
    
    if ($widget->can_add_feed())
    {
        echo "<tr>";
        echo "<td style='padding-top:10px'>";
        echo view('input/text', array(
            'id' => 'feed_url',
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
    }
    echo "</table>";
    echo "</form>";

    echo "<form id='feed_add_link_form' method='POST' action='{$widget->get_edit_url()}?action=add_link'>";
    echo view('input/securitytoken');
    echo view('input/hidden', array('name' => 'url'));
    echo "</form>";
                
    echo "<form id='feed_add_form' method='POST' action='{$widget->get_edit_url()}?action=add_feed'>";
    echo view('input/securitytoken');
    echo view('input/hidden', array('name' => 'url'));
    echo view('input/hidden', array('name' => 'feed_url'));
    echo view('input/hidden', array('name' => 'feed_subtype'));
    echo view('input/hidden', array('name' => 'add_link'));
    echo "</form>";

    echo "<form id='feed_remove_form' method='POST' action='{$widget->get_edit_url()}?action=remove_feed'>";
    echo view('input/securitytoken');
    echo view('input/hidden', array('name' => 'guid'));
    echo view('input/hidden', array('name' => 'remove_posts'));
    echo "</form>";
    
    $content = ob_get_clean();    
    
    echo view("section", array(
        'header' => __('widget:news:feeds'),
        'content' => $content
    ));    
?>
