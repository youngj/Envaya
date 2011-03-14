function Class() {}
function makeClass($base)
{
    $base = $base || Class;
    var $class = function() { this.init.apply(this, arguments); };
    var $proto = function() {};
    $proto.prototype = $base.prototype;
    $class.prototype = new $proto;
    return $class;
}

function addEvent(elem, type, fn)
{
    if (elem.addEventListener)
    {
        elem.addEventListener(type, fn, false);
    }
    else
    {
        elem.attachEvent('on' + type, fn);
    }
}

function removeEvent(elem, type, fn)
{
    if (elem.removeEventListener)
    {
        elem.removeEventListener(type, fn, false);
    }
    else
    {
        elem.detachEvent('on'+type, fn);
    }
}

function _eval(x)
{
    return eval("("+x+")");
}

var fetchJson = (function() {
    var _jsonCache = {};

    return function(url, fn, errorFn)
    {
        if (_jsonCache[url])
        {
            setTimeout(function() {
                fn(_jsonCache[url]);
            }, 1);
            return null;
        }
        else
        {
            var xhr = (window.ActiveXObject && !window.XMLHttpRequest) ? new ActiveXObject("Msxml2.XMLHTTP") : new XMLHttpRequest();
            xhr.onreadystatechange = function()
            {
                if (xhr.readyState == 4)
                {
                    var status = xhr.status;
                    if (status == 200)
                    {
                        fn(_jsonCache[url] = _eval(xhr.responseText));
                    }
                    else if (status == 500)
                    {
                        var data = _eval(xhr.responseText);                       
                        errorFn ? errorFn(data) : alert(data.error);
                    }
                    else if (status >= 400)
                    {
                        alert("HTTP Error " + status); 
                    }
                }
            };
            xhr.open("GET", url, true);
            xhr.send(null);
            return xhr;
        }
    }
})();
    
function bind(obj, fn)
{
    return function() {
        return fn(obj);
    };
}

function removeChildren(elem)
{
    while (elem.firstChild)
    {
        elem.removeChild(elem.firstChild);
    }
}

function removeElem(elem)
{
    if (elem.parentNode)
    {
        elem.parentNode.removeChild(elem);
    }
}


function createElem(/* args */)
{
    var elemType = arguments[0];

    var el = document.createElement(elemType);

    for (var i = 1; i < arguments.length; i++)
    {
        var arg = arguments[i];
        switch (typeof(arg))
        {
            case 'string':
                el.appendChild(document.createTextNode(arg));
                break;
            case 'object':
                if (arg != null)
                {
                    if (arg.nodeName)
                    {
                        el.appendChild(arg);
                    }
                    else
                    {
                        for (var key in arg)
                        {
                            if (arg.hasOwnProperty(key))
                            {
                                var val = arg[key];

                                if (typeof(val) == 'function')
                                {
                                    addEvent(el, key, val);
                                }
                                else
                                {
                                    el[key] = arg[key];
                                    //el.setAttribute(key, arg[key]);
                                }
                            }
                        }
                    }
                }
                break;
        }
    }

    return el;
}

window.dirty = false;
function setDirty($dirty)
{
    if ($dirty && !window.submitted)
    {
        if (!window.onbeforeunload)
        {
            window.onbeforeunload = function() {
                return __['page:dirty'];
            };
        }
    }
    else
    {
        window.onbeforeunload = null;
    }
    window.dirty = $dirty;

    return true;
}

/*
 * Needed for onclick in IE anchor tags with javascript: urls, 
 * since IE calls onbeforeunload in this case
 */
function ignoreDirty()
{
    var $dirty = window.dirty;
    setDirty(false);
    setTimeout(function() { setDirty($dirty) }, 5);    
}

function setSubmitted()
{
    setDirty(false);
    window.submitted = true;
    return true;
}

function addImageLink(img)
{    
    var match = /[\=\/](\d+)\/([\w\.]+)\/([\w\.]+)/.exec(img.src);
        
    if (match && match[3] != 'large.jpg')
    {
        img.style.cursor = 'pointer';
        addEvent(img, 'click', function() { 
            window.location = "/pg/large_img?owner=" + (match[1]) + "&group=" + match[2];
        });
    }     
}

function addImageLinks(container)
{
    if (container)
    {
        var imgs = container.getElementsByTagName('img');
        for (var i = 0; i < imgs.length; i++)
        {
            var img = imgs[i];
            if (img.parentNode.nodeName != 'A')
            {        
                addImageLink(img);
            }
        }
    }
}

function hideMessages(containerId)
{
    var div = document.getElementById(containerId);
    if (div)
    {
        div.style.display = 'none';
    }
}