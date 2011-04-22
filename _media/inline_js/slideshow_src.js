function slideshow(featuredPhotos, defaultPhoto) {
    function shuffle(a)
    {
        var i = a.length;
        while (i > 0) {
            var j = Math.floor(Math.random() * i);
            i--;
            var tmp = a[i];
            a[i] = a[j];
            a[j] = tmp;
        }
        return a;
    }
        
    var images = shuffle(featuredPhotos),
        currentIndex = -1,
        caption = createElem('a'),
        orgLink = createElem('a'),
        imgContainer = document.getElementById('home_banner_photo'),
        controls = document.getElementById('home_slideshow_controls');

    if (!images.length)
    {
        imgContainer.style.backgroundImage = "url("+defaultPhoto+")";
        document.getElementById('home_caption_shadow').style.display = 'none';
        return;
    }    
    
    controls.appendChild(createElem('div', {className: 'slideshow_caption'}, caption, orgLink));
        
    if (images.length > 1)
    {
        controls.appendChild(createElem('div', {className: 'slideshow_nav'},
            createElem('a', {
                    className: 'slideshow_nav_prev', 
                    href:'javascript:void(0)',
                    mouseover:function() { preloadIndex(currentIndex - 1); },
                    click:function() { setTimeout(function() { setCurrentIndex(currentIndex - 1); preloadIndex(currentIndex - 1); }, 10); }
                }, 
                createElem('span',"\xab")
            ),
            createElem('a', {
                    className: 'slideshow_nav_next', 
                    href:'javascript:void(0)', 
                    mouseover:function() { preloadIndex(currentIndex + 1); },
                    click:function() { setTimeout(function() { setCurrentIndex(currentIndex + 1); preloadIndex(currentIndex + 1); }, 10); }
                }, 
                createElem('span',"\xbb")
            )
        ));
    }
    
    function preloadIndex(index)
    {
        var image = images[(index + images.length) % images.length];        
        if (!image.elem && !image.preload)
        {
            image.preload = new Image();
            image.preload.src = image.url;            
        }   
    }
        
    function setCurrentIndex(index)
    {    
        index = (index + images.length) % images.length;
    
        if (currentIndex != -1)
        {
            images[currentIndex].elem.style.display = 'none';
        }
    
        var image = images[index];
    
        if (!image.elem)
        {
            var img = image.elem = createElem('img',{src:image.url});
            img.style.left = (-image.x || 0) + "px";
            img.style.top = (-image.y || 0) + "px";
            imgContainer.appendChild(img);
        }   
        image.elem.style.display = 'block';
        
        caption.href = image.href;
        orgLink.href = image.href;
        removeChildren(orgLink);
        removeChildren(caption);
        caption.appendChild(document.createTextNode(image.caption));
        orgLink.appendChild(document.createTextNode(image.org));
        
        currentIndex = index;

        var hasCaption = image.caption || image.org;        
    }
    
    function getStartIndex()
    {
        for (var i = 0; i < images.length; i++)
            if (images[i].weight >= Math.random())
                return i;
        return 0;
    }
    
    setCurrentIndex(getStartIndex());
}