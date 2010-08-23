<?php
    $widget = $vars['widget'];
    $org = $widget->get_container_entity();

    ob_start();
?>
<div class='section_content padded'>
<?php
echo view("widgets/edit_content", array('widget' => $widget));

?>
<div id='addTeamMember' class='modalBody' style='display:none'>
<div class='input'>
<label><?php echo __('widget:team:name'); ?></label>
<?php
    echo view('input/text',
        array(
            'internalname' => 'name',
            'internalid' => 'name',
            'trackDirty' => true,
            'js' => "style='width:350px'",
        )
    );
?>
</div>

<div class='input'>
<label><?php echo __('widget:team:description'); ?></label>
<?php
    echo view('input/longtext',
        array(
            'internalname' => 'description',
            'internalid' => 'description',
            'trackDirty' => true,
            'js' => "style='height:60px;width:350px'",
        )
    );
?>
</div>


<div class='input'>
<label><?php echo __('widget:team:photo'); ?></label><br />

<?php echo view('input/swfupload_image', array(
    'internalname' => 'image',
    'jsname' => 'teamImageUploader',
    'internalid' => 'image',
    'trackDirty' => true,
    'sizes' => array(
            'small' => '150x150',
            'large' => '450x450',
        )
)) ?>

</div>
</div>

<script type='text/javascript'>

var addTeamMember = document.getElementById('addTeamMember');
var modalBox;

function openAddTeamMember()
{
    var $dirty = window.dirty;
    setDirty(false);
    setTimeout(function() { setDirty($dirty) }, 5);

    function saveChanges()
    {
        var nameInput = document.getElementById('name');
        var descriptionInput = document.getElementById('description');

        var name = nameInput.value;

        if (!name)
        {
            alert(<?php echo json_encode(__('widget:team:name:missing')); ?>);
            nameInput.focus();
            return;
        }

        var description = descriptionInput.value;

        var container = createElem('div');
        container.style.clear = 'both';

        var image = window.teamImageUploader.getCurrentImage();
        if (image && image.small)
        {
            container.appendChild(createElem('img', {
                className: 'image_right',
                src: image.small.url,
                width: image.small.width,
                height: image.small.height
            }));
        }

        var heading = createElem('h3', name);
        container.appendChild(heading);

        if (description)
        {
            container.appendChild(createElem('p', description));
        }

        var ed = tinyMCE.activeEditor;
        ed.setContent(ed.getContent() + createElem('div', container).innerHTML);

        ed.getWin().scrollTo(0,9999);

        nameInput.value = '';
        descriptionInput.value = '';
        window.teamImageUploader.reset();

        modalBox.style.display = 'none';
    }

    function cancel()
    {
        modalBox.style.display = 'none';
    }

    if (!modalBox)
    {
        modalBox = tinyMCE.activeEditor.createModalBox(<?php echo json_encode(__('widget:team:add')) ?>, addTeamMember, saveChanges, cancel);
        document.body.appendChild(modalBox);
        addTeamMember.style.display = 'block';
    }
    else
    {
        modalBox.style.display = 'block';
    }

    setTimeout(function() {
        document.getElementById('name').focus();
    }, 1);
}

</script>

<a href='javascript:void(0)' onclick='openAddTeamMember()' style='font-weight:bold'>
<?php echo __('widget:team:add') ?>
</a>


</div>
<?php
    $content = ob_get_clean();

    echo view("widgets/edit_form", array(
            'widget' => $widget,
            'body' => $content
    ));
?>