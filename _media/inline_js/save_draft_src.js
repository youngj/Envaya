(function() {        
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
        setTimeout(function() {
            var ed = tinyMCE.activeEditor;
        
            lastSaveTime = new Date().getTime();
        
            var content = ed.getContent();        
            if (!content || content == lastSavedContent)
            {
                showSaveMessage(__['tinymce:no_unsaved_changes'], 3000);
                return;
            }
            
            var form = document.forms[0];
            
            showSaveMessage(__['tinymce:saving']);
            
            var xhr = getXHR(function(res) {             
                window.save_draft_guid = res.guid;
                lastSavedContent = content;
                ed.isNotDirty = true;            
                
                showSaveMessage(__['tinymce:saved'], 5000);
                setDirty(false);
            });

            xhr.open("POST", form.action, true);
            
            var params = "_draft=1&content="+encodeURIComponent(content) + "&__ts=" + form.__ts.value + "&__token=" + form.__token.value;
            
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.setRequestHeader("Content-Length", params.length);            
            xhr.send(params);    
        }, 10);
    };
    
    function restoreRevision(revision)
    {
        if (!tinyMCE.activeEditor.isDirty()
            || confirm(__['tinymce:restore_confirm']))
        {    
            var ed = tinyMCE.activeEditor;        
            ed.setContent(revision.content);
        
            closeModalBox();
        }
    }
    
    function previewRevision(revision)
    {
        modalBox.style.display = 'none';
        
        var previewElem = createElem('div', {
            className: 'revisionPreview',
            innerHTML: __['tinymce:loading']
        });
        
        var previewBox = createModalBox({
            title: __['tinymce:preview_older'],
            okText: __['tinymce:restore'],
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
            content: previewElem
        });        

        function setButtonsVisible($visible)
        {                        
            var inputs = previewBox.getElementsByTagName('input');
            var style = $visible ? 'inline' : 'none';
            inputs[0].style.display = style;
            inputs[1].style.display = style;            
        }
        
        setButtonsVisible(false);
        
        document.body.appendChild(previewBox);
        
        fetchJson("/pg/js_revision_content?id=" + revision.id, function(res) {
            revision.content = res.content;
            setButtonsVisible(true);
            previewElem.innerHTML = res.content;
        }, function(err) {
            removeChildren(previewElem);
            previewElem.appendChild(document.createTextNode(err.error));
        });
    }
    
    function makeRevisionElem(num, revision)
    {    
        var elem = createElem('div', {className:'revisionLink'},
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
        setTimeout(function() {
        
            var content = createElem('div', {className: 'padded'});
            
            content.appendChild(document.createTextNode(__['tinymce:loading']));
            
            closeModalBox();

            modalBox = createModalBox({
                title: __['tinymce:restoredraft_desc'],
                content: content,
                hideOk: true,
                cancelText: __['tinymce:close']
            });
            
            document.body.appendChild(modalBox);
            
            var xhr = getXHR(function(res) {
                    removeChildren(content);
                    var revisions = res.revisions;
                    
                    if (revisions.length == 0)
                    {
                        content.appendChild(document.createTextNode(__['tinymce:no_revisions']));
                    }
                    else
                    {
                        if (revisions.length > 15)
                        {
                            content.style.height = "246px";
                            content.style.overflow = "auto";
                        }
                    
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
            xhr.open("GET", '/pg/js_revisions?entity_guid=' + window.save_draft_guid, true);
            xhr.send(null);                
        }, 10);
    };
})();