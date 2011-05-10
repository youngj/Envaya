function _eval(x)
{
    return eval("("+x+")");
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

function $(id)
{
    return document.getElementById(id);
}