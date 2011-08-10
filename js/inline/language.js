function languageChanged()
{
    setTimeout(function() {
        var languageList = $('top_language');
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
        location.replace(urlWithParam(curUrl,'lang',newLang));
    }
    return false;
}
