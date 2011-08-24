function languageChanged()
{
    setTimeout(function() {
        setLang($('top_language').value);
    }, 1);
}

function setLang(newLang)
{
    var curUrl = location.href;
    var form = document.forms[0];
    if (form && form.method.toUpperCase()=='POST')
    {    
        form.action = '/pg/change_lang/?url='+encodeURIComponent(curUrl)+"&lang=" + newLang;
        setSubmitted();
        form.submit();
    }
    else
    {
        location.replace(urlWithParam(curUrl,'lang',newLang));
    }
    return false;
}
