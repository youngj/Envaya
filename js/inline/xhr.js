function rawXHR(successFn, errorFn)
{
    var xhr = (window.ActiveXObject && !window.XMLHttpRequest) ? new ActiveXObject("Msxml2.XMLHTTP") : new XMLHttpRequest();

    xhr.onreadystatechange = function()
    {
        if (xhr.readyState == 4)
        {
            var status = xhr.status;
            if (status == 200)
            {
                successFn.call(xhr, xhr.responseText);
            }
            else if (status >= 400)
            {
                (errorFn || httpError).call(xhr, xhr.responseText);
            }
        }
    };    
    return xhr;
}

function httpError()
{
    alert("HTTP Error " + this.status); 
}

function jsonXHR(successFn, errorFn)
{
    return rawXHR(function(res) { 
            successFn.call(this, _eval(res));
        },
        function (res) {
            var contentType = this.getResponseHeader('Content-Type');                        
            if (contentType == 'text/javascript')
            {
                var data = _eval(res);
                errorFn ? errorFn.call(this, data) : alert(data.error);                
            }
            else
            {
                httpError.call(this);
            }
        }
    );    
}

function asyncPost(xhr, action, params)
{
    var form = document.forms[0],
        paramArr = [];

    if (form && form.__ts)
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
            var xhr = jsonXHR(
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