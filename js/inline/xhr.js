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

function asyncPost(xhr, action, params)
{
    var form = document.forms[0],
        paramArr = [];

    if (form.__ts)
    {
        params.__ts = form.__ts.value;
        params.__token = form.__token.value;
    }
    
    for (var name in params)
    {
        paramArr.push(name + '=' + encodeURIComponent(params[name]));
    }
    var paramStr = paramArr.join('&');
    
    xhr.open("POST", action, true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.setRequestHeader("Content-Length", paramStr.length);            
    xhr.send(paramStr);
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