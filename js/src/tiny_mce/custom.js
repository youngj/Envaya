(function(tinymce) {
    tinymce.extend(tinymce.Editor.prototype, {
        updateContent: function()
        {
            var t = this;
            if (t.codeMode)
            {            
                t.updateContentFromCode();
            }
        },        
         
        updateContentFromCode: function()
        {
            if (this.aceEditor)
            {
                this.setContent(this.aceEditor.getSession().getValue());
            }
        },        
         
        updateCodeFromContent: function()
        {
            if (this.aceEditor)
            {
                this.aceEditor.getSession().setValue(this.getContent({no_events: true}));
            }
        },
         
        insertOrUpdateImage: function(imgNode, imgArgs)
        {
            if (!imgNode)
            {
                this.execCommand('mceInsertContent', false, '<img id="__mce_tmp" />', {skip_undo : 1});
                this.dom.setAttribs('__mce_tmp', imgArgs);
                this.dom.setAttrib('__mce_tmp', 'id', '');
                this.undoManager.add();                
            }
            else
            {
                this.dom.setAttribs(imgNode, imgArgs);
                this.execCommand('mceRepaint');
            }                     
        }
     });
     
     tinymce.create('tinymce.themes.CustomTheme:tinymce.themes.AdvancedTheme', {

		controls : {
			bold : ['bold_desc', 'Bold'],
			italic : ['italic_desc', 'Italic'],
			underline : ['underline_desc', 'Underline'],
			strikethrough : ['striketrough_desc', 'Strikethrough'],
			justifyleft : ['justifyleft_desc', 'JustifyLeft'],
			justifycenter : ['justifycenter_desc', 'JustifyCenter'],
			justifyright : ['justifyright_desc', 'JustifyRight'],
			justifyfull : ['justifyfull_desc', 'JustifyFull'],
			bullist : ['bullist_desc', 'InsertUnorderedList'],
			numlist : ['numlist_desc', 'InsertOrderedList'],
			outdent : ['outdent_desc', 'Outdent'],
			indent : ['indent_desc', 'Indent'],
			link : ['link_desc', 'mceLink'],
			unlink : ['unlink_desc', 'unlink'],
			image : ['image_desc', 'mceImage'],
            document : ['document_desc', 'mceDocument'],
            save : ['save_desc', 'mceSave'],
            restoredraft : ['restoredraft_desc', 'mceRestoreDraft'],
			code : ['code_desc', 'mceCodeEditor'],
			removeformat : ['removeformat_desc', 'RemoveFormat'],
			blockquote : ['blockquote_desc', 'mceBlockQuote']
		},

		stateControls : ['bold', 'italic', 'underline', 'bullist', 'numlist', 'justifyleft', 'justifycenter', 'justifyright', 'blockquote'],     
     
        init: function(ed, url) {
            this.parent(ed, url);

            var t = this, 
                s = t.settings;            
            
            if (s.save_draft_callback)
            {
                ed.addShortcut('ctrl+s', 'save', 'mceSave');
            }                    
            
            ed.onBeforeGetContent.add(function(ed, o)
            {                       
                ed.updateContent();
            
                var body = ed.getBody();

                var invalidTagNames = ['meta','style','title','link'];
                for (var j = 0; j < invalidTagNames.length; j++)
                {
                    var badTags = body.getElementsByTagName(invalidTagNames[j]), badTagsCopy = [];

                    for (var i = 0; i < badTags.length; i++)
                    {
                        badTagsCopy.push(badTags[i]);
                    }
                    for (var i = 0; i < badTagsCopy.length; i++)
                    {
                        removeElem(badTagsCopy[i]);
                    }
                }

                var paragraphs = body.getElementsByTagName('p');
                for (var i = 0; i < paragraphs.length - 1; i++)
                {
                    paragraphs[i].className = '';
                }
                if (paragraphs.length > 0)
                {
                    paragraphs[i].className = 'last-paragraph';

                    if (paragraphs[0].childNodes.length == 0)
                    {
                        removeElem(paragraphs[0]);
                    }
                }
            });

            ed.onDblClick.add(function(ed, e) {
                var target = e.target;
                if (target)
                {
                    if (target.nodeName == 'IMG')
                    {
                        if (target.className.indexOf('scribd_placeholder') != -1)
                        {
                            ed.execCommand('mceDocument');
                        }
                        else
                        {
                            ed.execCommand('mceImage');
                        }
                    }
                    else if (target.nodeName == 'A')
                    {
                        ed.execCommand('mceLink');
                    }
                }
            });

            ed.onChange.add(function(ed, l) {
                if (ed.isDirty())
                {
                    setDirty(true);
                }
            });            
        },
        
        _mceCodeEditor : function(ui, val) {
            var ed = this.editor;
            var self = this;            
            
            ed.codeMode = !ed.codeMode;            
            
            if (!ed.aceContainer && ed.codeMode)
            {
                var aceContainer = ed.aceContainer = createElem('div', {
                    className: 'input-code'                
                }, createElem('div', {className:'padded'}, "Loading..."));
                
                aceContainer.style.border = '0px';
                aceContainer.style.backgroundColor = '#fff';
                            
                var contentContainer = ed.contentAreaContainer;                

                contentContainer.appendChild(aceContainer);
                
                function initAceEditor()
                {
                    var aceEditor = ed.aceEditor = ace.edit(aceContainer);    
                    var mode = require("ace/mode/html").Mode;    
        
                    var session = aceEditor.getSession();
                    
                    aceEditor.renderer.setShowPrintMargin(false);
                    aceEditor.renderer.setHScrollBarAlwaysVisible(false);
                    session.setUseWrapMode(true);
                    session.setWrapLimitRange(null, null);    
                    session.setMode(new mode());    
                    
                    session.on('change', function() { 
                        setDirty(true);                     
                    });
                    
                    aceEditor.commands.addCommand({
                        name: "save",
                        bindKey: {
                            win: "Ctrl-S",
                            mac: "Command-S",
                            sender: "editor"
                        },
                        exec: function() 
                        {
                            if (self.settings.save_draft_callback)
                            {
                                self.settings.save_draft_callback(ed);
                            }
                        }
                    });  
                    ed.updateCodeFromContent();
                    ed.aceEditor.focus();    
                }
                
                if (!window.ace)
                {
                    window.onAceLoaded = initAceEditor;
                    var script = createElem('script', {src:'/_media/tiny_mce_ace.js', type:'text/javascript'});
                    document.body.appendChild(script);
                }       
                else
                {
                    initAceEditor();
                }
            }
            
            var on = ed.codeMode;            
            
            var iframe = $(ed.id + "_ifr");
            
            iframe.style.display = on ? 'none' : 'block';
            ed.aceContainer.style.display = on ? 'block' : 'none';
                                        
            if (on)
            {
                ed.updateCodeFromContent();
                if (ed.aceEditor)
                {
                    ed.aceEditor.focus();    
                }
            }
            else
            {
                ed.updateContentFromCode();
            }            
            
            var cm = ed.controlManager;
            cm.setActive('code', on);            
            
            var cmds = ['bold','italic','underline','bullist','numlist',
                'outdent','indent','blockquote','link','image','document',
                'justifyleft','justifycenter','justifyright','formatselect'];
            each(cmds, function(cmd) { cm.setDisabled(cmd, on); });
        },

        _mceSave: function(ui, val)
        {            
            this.settings.save_draft_callback(this.editor);
        },

        _mceRestoreDraft: function(ui, val)
        {            
            this.settings.restore_draft_callback(this.editor);
        },
        
        _mceDocument: function(ui, val)
        {
            var ed = this.editor;   
        
            var e = ed.selection.getNode();
            var imageNode = (e && e.nodeName == 'IMG') ? e : null;
            
            var range = ed.selection.getRng();            
            
            var guid = '';
            if (imageNode && imageNode.alt)
            {
                var metadata = imageNode.alt.split(':');
                guid = metadata[3];
            }
        
            var iframeName = 'modalDocumentFrame_'+Math.ceil(Math.random()*1000000);
        
            var iframe = createElem('iframe',
                 {
                    src:'/pg/select_document?r='+Math.random()+"&guid="+escape(guid)+"&frameId="+iframeName,
                    scrolling:'no',
                    frameBorder:'0',
                    border:'0',
                    className:'modalDocumentFrame',
                    name:iframeName,
                    id:iframeName
                 }                  
             );

            var loading = createElem('div', {className:'modalImageFrameLoading'}, __('loading'));
             
            var imageBox = createModalBox({               
                title: ed.getLang(imageNode ? 'document_edit' : 'document_insert'),
                content: createElem('div',
                         {className:'modalBody'},
                         loading,
                         iframe
                     ),
                okFn: saveChanges, 
                cancelFn: cancel,
                width: 640
            });               
             
            function saveChanges()
            {
                var iframeWindow = window.frames[iframeName];                
                var uploadedFile = iframeWindow.getUploadedFile();
                
                ed.selection.setRng(range);

                if (uploadedFile)
                {
                    ed.insertOrUpdateImage(imageNode, {
                        src: "/_media/images/document_icon.jpg",
                        width: '100%',
                        height: '300',
                        'class': 'scribd_placeholder',
                        alt: uploadedFile.filename+':'+uploadedFile.docid+':'+uploadedFile.accesskey+':'+uploadedFile.guid
                    });               
                }            
                removeElem(imageBox); 
            }           
            
            function cancel()
            {
                removeElem(imageBox); 
            }
            
            window["frameapi_" + iframeName] = {
                'saveChanges': saveChanges,
                'cancel': cancel,
                'iframe': iframe,
                'loading': loading
            };
                                                              
            document.body.appendChild(imageBox);                  

            setTimeout(function() {
                iframe.focus();
            }, 1);            
        },
        
        _mceImage : function(ui, val) {
 
            var ed = this.editor;   
            
            var e = ed.selection.getNode();
            var imageNode = (e && e.nodeName == 'IMG') ? e : null;
            var src = imageNode ? e.src : '';
            var pos = '';
            if (imageNode)
            {
                var match = /image_(\w+)/.exec(imageNode.className);
                if (match)
                {
                    pos = match[1];
                }
            }            
            
            var iframeName = 'modalImageFrame_'+Math.ceil(Math.random()*1000000);
            
            var range = ed.selection.getRng();            
            
            var iframe = createElem('iframe',
                 {
                    src:'/pg/select_image?r='+Math.random()+"&src="+escape(src)+"&pos="+escape(pos)+"&frameId="+iframeName,
                    scrolling:'no',
                    frameBorder:'0',
                    border:'0',
                    className:'modalImageFrame',
                    name:iframeName,
                    id:iframeName
                 }                  
             );

            var loading = createElem('div', {className:'modalImageFrameLoading'}, __('loading'));
            var imageBox = createModalBox({
                title: ed.getLang(imageNode ? 'image_edit' : 'image_insert'),
                content: createElem('div',
                         {className:'modalBody'},
                         loading,
                         iframe
                     ),
                okFn: saveChanges, 
                cancelFn: cancel
            });       

            function saveChanges()
            {
                ed.selection.setRng(range);
            
                var iframeWindow = window.frames[iframeName];
                
                var selectedImage = iframeWindow.getSelectedImage();                
                if (selectedImage)
                {
                    var pos = iframeWindow.getSelectedPosition();                                                          
                    ed.insertOrUpdateImage(imageNode, {
                        src: selectedImage.url,
                        width: selectedImage.width,
                        height: selectedImage.height,
                        'class': 'image_' + pos
                    });                         
                }
            
                removeElem(imageBox); 
            }
            
            function cancel()
            {
                removeElem(imageBox); 
            }
            
            window["frameapi_" + iframeName] = {
                'saveChanges': saveChanges,
                'cancel': cancel,
                'iframe': iframe,
                'loading': loading
            };            
                                                              
            document.body.appendChild(imageBox);        

            setTimeout(function() {
                iframe.focus();
            }, 1);
        },

        _mceLink : function(ui, val) {
            var ed = this.editor;

            var content = ed.selection.getContent({format: 'text'});
            
            var selectedNode = ed.selection.getNode();
            
            var e = ed.dom.getParent(selectedNode, 'A');
            var imageLink = selectedNode && selectedNode.nodeName == 'IMG';
            
            var range = ed.selection.getRng();
            
            var textField = createElem('input', {type:'text', className:'input-text', value:(e ? (e.innerText || e.textContent || '') : content)});
            
            var textDiv = imageLink ? createElem('div') : createElem('div',
                {className:'linkText'},
                createElem('h3', ed.getLang('link_text')),
                textField
            );
            
            var urlField = createElem('input', {
                type:'text',       
                className:'input-text', 
                value:(e ? e.href : '')
            });
            var linkBox = createModalBox({
                title: ed.getLang(e ? 'link_edit' : 'link_insert'),
                content: createElem('div',
                        {className:'modalBody'},
                        createElem('div',
                            {className:'linkUrl'},
                                createElem('h3', ed.getLang('link_url')),
                            urlField,
                            createElem('div', {className:'help'}, 
                                ed.getLang('link_url_help')
                            ),
                            createElem('div',
                                createElem('a', 
                                    { 
                                        href:'javascript:void(0)',
                                        click: function() {
                                            var url = urlField.value;
                                            if (url)
                                            {
                                                window.open(urlField.value);
                                            }
                                            else
                                            {
                                                alert(ed.getLang('link_url_empty'));
                                            }                                        
                                        }
                                    },
                                    ed.getLang('link_url_test')
                                )
                            )
                        ), 
                        textDiv
                    ),
                okFn: saveChanges,
                focus: true
            });       
            
            function saveChanges()
            {
                ed.selection.setRng(range);

                var url = urlField.value;

                if (url)
                {       
                    if (!e)
                    {       
                        if (ed.selection.isCollapsed())
                        {
                            var randid = 'mce_temp_wtf' + Math.ceil(Math.random() * 10000);
                            ed.selection.setContent("<span id='"+randid+"'>TEMP</span>");    
                            var wtf = ed.getDoc().getElementById(randid);
                            ed.selection.select(wtf);
                        }

                        ed.getDoc().execCommand("CreateLink", false, "#mce_temp_url#");

                        tinymce.each(ed.dom.select("a"), function(n) {
                            if (ed.dom.getAttrib(n, 'href') == '#mce_temp_url#') {
                                e = n;
                            }
                        });   
                    }    

                    if (e)
                    {
                        e.href = url;  
                        e.setAttribute('_mce_href', url);
                        
                        if (!imageLink)
                        {
                            removeChildren(e);
                            e.appendChild(ed.getDoc().createTextNode(textField.value || url));
                        }    
                    }    
                }    
                else if (e)
                {
                    ed.selection.select(e);
                    ed.getDoc().execCommand("unlink", false, null);
                }
                removeElem(linkBox); 
            }          
            
            document.body.appendChild(linkBox);        
        }        
     });
     
     tinymce.ThemeManager.add('custom', tinymce.themes.CustomTheme);
})(tinymce);