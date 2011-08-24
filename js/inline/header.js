function _eval(x)
{
    return eval("("+x+")");
}

function __(key)
{
    return jsStrs[key] || key;
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

var trackDirty = (function() {
    // ignore keys that just move cursor (tab, arrows, home/end, pg up/down)
    var cleanKeys = {9:1,33:1,34:1,35:1,36:1,37:1,38:1,39:1,40:1};

    return function(evt)
    {
        evt = evt || window.event;
        if (!cleanKeys[evt.keyCode] && !evt.ctrlKey && !evt.altKey)
        {
            setDirty(true);
        }
    };
})();

window.dirty = false;
function setDirty($dirty)
{
    if ($dirty && !window.submitted)
    {
        if (!window.onbeforeunload)
        {
            window.onbeforeunload = function() {
                return __('page:dirty');
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

function $(id)
{
    return document.getElementById(id);
}

function urlWithParam(url, param, value)
{
    var qPart = param + '=' + value,
        qIndex = url.indexOf('?') + 1,
        query = url.substr(qIndex),
        parts = query.split('&'),
        i = 0;        
        
    if (!qIndex)
    {
        return url + '?' + qPart;
    }
    for (; i < parts.length; i++)    
    {
        if (!parts[i].indexOf(param + '='))
        {
            parts.splice(i--, 1);
        }
    }

    parts.splice(0,0,qPart);
    return url.substr(0, qIndex) + parts.join('&');
}

function each(items, fn)
{
    var res = [], i = 0;
    while (i < items.length) 
    {
        res.push(fn(items[i++]));    
    }
    return res;
}