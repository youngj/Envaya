each($('main_content').getElementsByTagName('img'), function(img){    

    if (img.parentNode.nodeName != 'A')
    {        
        var match = /[\=\/](\w+)\/([\w\.]+)\/(small|medium)\.jpg/.exec(img.src);
            
        if (match)
        {
            img.style.cursor = 'pointer';
            addEvent(img, 'click', function() { 
                location = "/pg/large_img?group=" + match[2];
            });
        }     
    }
});