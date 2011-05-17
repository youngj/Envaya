<div id='addTeamMember' class='modalBody' style='display:none'>
<div class='input'>
<label><?php echo __('widget:team:name'); ?></label>
<?php
    echo view('input/text',
        array(
            'name' => 'name',
            'id' => 'name',
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
            'name' => 'description',
            'id' => 'description',
            'trackDirty' => true,
            'js' => "style='height:60px;width:350px'",
        )
    );
?>
</div>


<div class='input'>
<label><?php echo __('widget:team:photo'); ?></label><br />

<?php echo view('input/swfupload_image', array(
    'name' => 'image',
    'jsname' => 'teamImageUploader',
    'id' => 'image',
    'trackDirty' => true,
    'sizes' => array(
            'small' => '150x150',
            'large' => '450x450',
        )
)) ?>

</div>
</div>

<script type='text/javascript'>
<?php 
    echo view('js/create_modal_box');
    echo view('js/dom');
?>
var addTeamMember = $('addTeamMember');
var modalBox;

function openAddTeamMember()
{
    function saveChanges()
    {
        var nameInput = $('name');
        var descriptionInput = $('description');

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
        var small = window.teamImageUploader.getFileByProp(image, 'size', 'small');
        
        if (small)
        {
            container.appendChild(createElem('img', {
                className: 'image_right',
                src: small.url,
                width: small.width,
                height: small.height
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
        modalBox = createModalBox({
            title: <?php echo json_encode(__('widget:team:add')) ?>, 
            content: addTeamMember, 
            okFn: saveChanges, 
            cancelFn: cancel
        });
        document.body.appendChild(modalBox);
        addTeamMember.style.display = 'block';
    }
    else
    {
        modalBox.style.display = 'block';
    }

    setTimeout(function() {
        $('name').focus();
    }, 1);
}

</script>

<a href='javascript:openAddTeamMember()' onclick='ignoreDirty()' style='font-weight:bold'>
<?php echo __('widget:team:add') ?>
</a>
