<div class='section_content padded'>
<?php 
    $widget = $vars['widget'];
    $org = $widget->get_root_container_entity();
    
    $query = $org->query_external_sites();    
    $sites = $query->filter();
    
    ob_start();
?>
<script type='text/javascript'>
<?php echo view('js/xhr'); ?>
<?php echo view('js/create_modal_box'); ?>

function setLoadingHTML(html)
{
    $('loading_msg').innerHTML = html || '\xa0';
}

function linkComplete(res)
{
    if (res.error)
    {
        linkError(res);
    }
    else
    {   
        linkSuccess(res);
    }
}

function linkSuccess(res)
{
    setLoadingHTML(''); 

    var titleField = createElem('input', {
        type:'text', 
        className:'input-text', 
        value: res.url
    });    
    
    var modalBox = createModalBox({
        title: <?php echo json_encode(__('widget:links:options')); ?>, 
        content: createElem('div', {className:'modalBody'},
            createElem('div', {className:'linkUrl'},
                createElem('h3', <?php echo json_encode(__('tinymce:link_url')); ?>),
                createElem('div', {className:'padded'}, res.url)
            ),
            createElem('div', {className:'linkText'},
                createElem('h3', <?php echo json_encode(__('tinymce:link_text')); ?>),
                titleField
            )
        ),
        okFn: function() {
            var form = document.forms[0];
            form.url.value = res.url;
            form.title.value = titleField.value;
            form.action.value = 'add';
            form.submit();
        },
        focus: true
    });

    document.body.appendChild(modalBox);                    
}

function handleEnter(e)
{
    e = window.event ? event : e;
    var code = e.charCode || e.keyCode;
    if (code == 13) // enter
    {
        getLinkInfo();
        if (e.preventDefault) e.preventDefault();
        e.returnValue = false;
    }
}

function linkError(res)
{
    setLoadingHTML('');
    
    var error = res.error;
    alert(error);
}

function getLinkInfo()
{
    var url = $('url').value;
    
    if (!url)
    {
        alert(<?php echo json_encode(__('widget:links:blank')); ?>);
        return;
    }
    
    setLoadingHTML(<?php echo json_encode(__('loading')); ?>);
    
    var xhr = getXHR(linkComplete, linkError);
    
    asyncPost(xhr, <?php echo json_encode($widget->get_edit_url()); ?>, {
        action: 'linkinfo_js',
        url: url
    });
}
</script>
<?php
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
            'confirm' => __('widget:links:confirm_delete')
        ));
        echo "</td>";
        echo "</tr>";
    }
    echo "<tr>";
    echo "<td style='padding-top:10px'>";
    echo view('input/text', array(
        'name' => 'url',
        'js' => "onkeypress='handleEnter(event)'",
        'id' => 'url',
    ));
    echo "<div id='loading_msg'>&nbsp;</div>";
    echo "</td>";
    echo "<td>";
    echo view('input/button', array(
        'type' => 'button',
        'value' => __('widget:links:add'),
        'js' => "onclick='getLinkInfo();' style='margin:0px'"
    ));
    echo "</td>";
    echo "</tr>";
    echo "</table>";

    echo view('input/hidden', array('name' => 'title'));
    echo view('input/hidden', array('name' => 'action'));
    echo view('focus', array('name' => 'url'));
    
    $content = ob_get_clean();
    
    echo view("widgets/edit_form", array(
        'widget' => $widget,
        'body' => $content
    ));
?>
</div>