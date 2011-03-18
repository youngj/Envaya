function createModalBox(options)
{
    /*
     * Options include:
     * -- width (int, px)
     * -- title (string)
     * -- content (string, dom element, or other argument accepted by createElem)
     * -- cancelFn (function, defaults to closing the box)
     * -- cancelText (string, default __['cancel']
     * -- hideCancel (bool)
     * -- okFn (function, defaults to closing the box)
     * -- okText (string, default __['ok'])
     * -- hideOk (bool)
     * -- focus (bool)
     */

    var removeFn = function() { removeElem(container); },
        okFn = options.okFn || removeFn,
        cancelFn = options.cancelFn || removeFn;
     
    var box = createElem('div',
            {
                className:'modalBox',
                keypress: function(e) {
                    e = window.event ? event : e;
                    var code = e.charCode || e.keyCode;
                    if (code == 13)
                    {
                        okFn();
                    }
                    else if (code == 27)
                    {
                        cancelFn();
                    }
                }   
            },                    
            createElem('div', 
                {
                    className:'modalHeading'
                },
                createElem('div', {className:'modalClose', click:cancelFn}),
                options.title
            ),
            options.content,
            createElem('div',
                {className:'modalButtons'},
                (options.hideOk ? '' : createElem('input', {
                    type:'submit', 
                    value:options.okText || __['ok'],
                    click: okFn
                })),
                ' ',
                (options.hideCancel ? '' : createElem('input', {
                    type:'submit', 
                    value:options.cancelText || __['cancel'],
                    click: cancelFn
                }))
            )                    
    ),    
        shadow = createElem('div', { className: 'modalShadow' }),
        win = window,
        doc = document,
        width = options.width || 400;
    
    var windowWidth = doc.body.offsetWidth || win.innerWidth,
        scrollTop = win.pageYOffset || doc.documentElement.scrollTop || doc.body.scrollTop,
        height = doc.documentElement.scrollHeight || doc.body.scrollHeight,
        container = createElem('div', shadow, box);                              
    
    box.style.width = width + 'px';
    box.style.left = (windowWidth / 2 - width / 2) + 'px';
    box.style.top = (scrollTop + 100) + 'px';
    shadow.style.height = height + 'px';

    if (options.focus)
    {
        setTimeout(function() {
            var inputs = container.getElementsByTagName('input'); // assumes it's not type='hidden'...
            if (inputs.length)
            {
                inputs[0].focus();
            }
        }, 5);
    }
    
    return container;
}
