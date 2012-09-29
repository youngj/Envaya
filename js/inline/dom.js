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

function setElemText(elem, text)
{
    removeChildren(elem);
    elem.appendChild(document.createTextNode(text));
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
                                if (key == 'style')
                                {                                    
                                    for (var p in val)
                                    {
                                        el[key][p] = val[p];
                                    }
                                }
                                else if (typeof(val) == 'function')
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