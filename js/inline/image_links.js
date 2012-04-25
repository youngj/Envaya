each($('main_content').getElementsByTagName('img'), function(img){    

    if (img.parentNode.nodeName != 'A')
    {        
        var match = /[\=\/](\w+)\/([\w\.]+)\/([\w\.]+)/.exec(img.src);
            
        if (match && match[3] != 'large.jpg')
        {
            img.style.cursor = 'pointer';
            addEvent(img, 'click', function() { 
                location = "/pg/large_img?group=" + match[2];
            });
        }     
    }
});