function postLink(url, ts, token)
{
    var form = createElem('form', {
            action: url,
            method: 'POST'
        },
        createElem('input', {name:'__ts', type:'hidden', value:ts}),
        createElem('input', {name:'__token', type:'hidden', value:token})
    );
    document.body.appendChild(form);    
    setTimeout(function() {
        form.submit();
    }, 2);
}
