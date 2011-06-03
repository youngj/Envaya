function addImageLinks(container)
{
    function addImageLink(img)
    {    
        var match = /[\=\/](\d+)\/([\w\.]+)\/([\w\.]+)/.exec(img.src);
            
        if (match && match[3] != 'large.jpg')
        {
            img.style.cursor = 'pointer';
            addEvent(img, 'click', function() { 
                window.location = "/pg/large_img?owner=" + (match[1]) + "&group=" + match[2];
            });
        }     
    }

    if (container)
    {
        var imgs = container.getElementsByTagName('img');
        for (var i = 0; i < imgs.length; i++)
        {
            var img = imgs[i];
            if (img.parentNode.nodeName != 'A')
            {        
                addImageLink(img);
            }
        }
    }
}