<?php
    $widget = $vars['widget'];
    $org = $widget->get_container_entity();   
    
    $lastRevision = $widget->guid ? 
        ContentRevision::query()
            ->where('entity_guid = ?', $widget->guid)
            ->order_by('time_created desc')
            ->get() 
        : null;        
        
    ?>

    <script type='text/javascript'>
(function() {        
    var guid = <?php echo json_encode($widget->guid); ?>;
    var modalBox = null;
    var lastSavedContent = null;   
    
    var lastSaveTime = new Date().getTime();

    // autosave
    setInterval(function() 
    {
        if (!window.tinyMCE || !tinyMCE.activeEditor)
            return;
    
        if (new Date().getTime() - lastSaveTime > 150000 && tinyMCE.activeEditor.isDirty())
        {
            saveDraft();
        }
    }, 
    30000);
    
    var saveMessageTimeout = null;
    
    function showSaveMessage(message, timeout)
    {
        if (saveMessageTimeout)
        {
            clearTimeout(saveMessageTimeout);
        }
        
        var saved = document.getElementById('saved_message');
        removeChildren(saved);
        saved.appendChild(document.createTextNode(message));
        saved.style.display = 'inline';
        
        if (timeout)
        {
            saveMessageTimeout = setTimeout(function() {
                saved.style.display = 'none'; 
                saveMessageTimeout = null;
            }, 3000);
        }
    }

    var _setDirty = setDirty;
    
    window.setDirty = function($dirty)
    {   
        return _setDirty($dirty);
    };            
    
    window.saveDraft = function()
    {               
        var ed = tinyMCE.activeEditor;
    
        lastSaveTime = new Date().getTime();
    
        var content = ed.getContent();        
        if (!content || content == lastSavedContent)
        {
            showSaveMessage("No unsaved changes.", 3000);
            return;
        }
        
        var form = document.forms[0];
        
        showSaveMessage("Saving...");
        
        var xhr = getXHR(function(res) { 
            guid = res.guid;
            lastSavedContent = content;
            ed.isNotDirty = true;            
            
            showSaveMessage("Changes saved.", 5000);
            setDirty(false);
        });

        xhr.open("POST", form.action, true);
        
        var params = "_draft=1&content="+encodeURIComponent(content) + "&__ts=" + form.__ts.value + "&__token=" + form.__token.value;
        
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.setRequestHeader("Content-Length", params.length);            
        xhr.send(params);    
    };
    
    function restoreRevision(revision)
    {
        if (!tinyMCE.activeEditor.isDirty()
            || confirm("Are you sure you want to restore this revision? Any changes that have not been saved will be lost."))
        {    
            var ed = tinyMCE.activeEditor;        
            ed.setContent(revision.content);
        
            closeModalBox();
        }
    }
    
    function previewRevision(revision)
    {
        modalBox.style.display = 'none';
        
        var previewBox = createModalBox({
            title: "Preview Older Version",
            okText: 'Restore',
            focus: true,
            width: 580,
            okFn: function() 
            {
                removeElem(previewBox);
                restoreRevision(revision);
            },
            cancelFn: function() {
                modalBox.style.display = 'block';
                removeElem(previewBox);
            },
            content: createElem('div', {
                className: 'revisionPreview',
                innerHTML: revision.content
            })
        });        
        document.body.appendChild(previewBox);
    }
    
    function makeRevisionElem(num, revision)
    {    
        var elem = createElem('div',
            num + ". ",
            createElem('a', {
                href: 'javascript:void(0);',
                click: function() {
                    ignoreDirty();
                    setTimeout(function() { previewRevision(revision); }, 1);
                }
            }, revision.friendly_time)
        );
        return elem;
    }
    
    function closeModalBox()
    {
        if (modalBox)
        {
            removeElem(modalBox);
            modalBox = null;
        }            
    }
    
    window.showOlderVersions = function()
    {        
        var content = createElem('div', {className: 'padded'});
        
        content.appendChild(document.createTextNode('Loading...'));
        
        closeModalBox();

        modalBox = createModalBox({
            title: "Older Versions",
            content: content,
            hideOk: true
        });
        
        document.body.appendChild(modalBox);
        
        var xhr = getXHR(function(res) {
                removeChildren(content);
                var revisions = res.revisions;
                
                if (revisions.length == 0)
                {
                    content.appendChild("No revisions found.");
                }
                else
                {
                    for (var i = 0; i < revisions.length; i++)
                    {
                        content.appendChild(makeRevisionElem(revisions.length - i, revisions[i]));
                    }
                }
            },
            function (err)
            {
                removeChildren(content);
                content.appendChild(document.createTextNode(err.error));
            }
        );
        xhr.open("GET", '/pg/js_revisions?entity_guid=' + guid, true);
        xhr.send(null);
                
    };
})();
</script>       
<div class='input' style='padding-bottom:0px'>
    <label><?php 
    
        $labelCode = "widget:{$widget->widget_name}:label";
        $label = __($labelCode);
        if ($label != $labelCode)
        {
            echo $label;
        }
    ?></label>
    <?php
        $helpCode = "widget:{$widget->widget_name}:help";
        $help = __($helpCode);
        if ($help != $helpCode)
        {
            echo "<div class='help'>$help</div>";
        }
        else
        {
            echo "<br />";
        }
    ?>
    
    <?php echo view("input/tinymce", array(
        'name' => 'content',
        'autoFocus' => true,
        'trackDirty' => true,
        'valueIsHTML' => $widget->has_data_type(DataType::HTML),
        'saveFn' => 'saveDraft',
        'restoreDraftFn' => 'showOlderVersions',
        'value' => $lastRevision ? $lastRevision->content : $widget->content
    )); ?>
        
    <div>
    <span id='saved_message' style='font-weight:bold;display:none'></span>&nbsp;
    </div>        
</div>
