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

function getXHR(successFn, errorFn)
{
    var xhr = (window.ActiveXObject && !window.XMLHttpRequest) ? new ActiveXObject("Msxml2.XMLHTTP") : new XMLHttpRequest();

    xhr.onreadystatechange = function()
    {
        if (xhr.readyState == 4)
        {
            var status = xhr.status;
            if (status == 200)
            {            
                successFn(_eval(xhr.responseText));
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
    return xhr;
}

var fetchJson = (function() {
    var _jsonCache = {};

    return function(url, successFn, errorFn)
    {
        if (_jsonCache[url])
        {
            setTimeout(function() {
                successFn(_jsonCache[url]);
            }, 1);
            return null;
        }
        else
        {
            var xhr = getXHR(
                function(result) {
                    successFn(_jsonCache[url] = result);
                },
                errorFn
            );
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

function languageChanged()
{
    setTimeout(function() {
        var languageList = document.getElementById('top_language');
        setLang(languageList.options[languageList.selectedIndex].value);
    }, 1);
}

function setLang(newLang)
{
    var curUrl = window.location.href;
    var form = document.forms[0];
    if (form && form.method.toUpperCase()=='POST')
    {    
        form.action = '/pg/change_lang/?url='+encodeURIComponent(curUrl)+"&lang=" + newLang;
        setSubmitted();
        form.submit();
    }
    else
    {
        var newUrl = curUrl.replace(/\blang\=\w+/, 'lang='+newLang);
        if (newUrl == curUrl)
        {
            newUrl = curUrl + ((curUrl.indexOf('?') != -1) ? '&' : '?') + "lang=" + newLang;
        }
        window.location.href = newUrl;
    }

    return false;
}

function openShare(url, name, width, height)
{
    var w = window.open(url, name, 'toolbar=0,resizable=1,scrollbars=1,status=0,width='+width+',height='+height);
    if (window.focus)
    {
        w.focus();
    }    
}

function fbShare() 
{
    openShare('http://www.facebook.com/sharer.php?u='+encodeURIComponent(window.canonicalUrl)+'&t='+encodeURIComponent(document.title),
        'fbshare',626,436);
}

function twitterShare()
{
    openShare('https://twitter.com/?status=' + encodeURIComponent(document.title + " - " + window.canonicalUrl),'twshare',626,436);
}

function emailShare(username)
{
    openShare('/' + username + '/share?u=' + encodeURIComponent(window.canonicalUrl),'eshare',726,636);
}