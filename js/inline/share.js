function openShare(url, name, width, height)
{
    var w = window.open(url, name, 'toolbar=0,resizable=1,scrollbars=1,status=0,width='+width+',height='+height);
    if (window.focus)
    {
        w.focus();
    }
}

function fbShare() 
{
    openShare('http://www.facebook.com/sharer.php?u='+encodeURIComponent(window.canonicalUrl)+'&t='+encodeURIComponent(document.title),
        'fbshare',626,436);
}

function twitterShare()
{
    openShare('https://twitter.com/home?status=' + encodeURIComponent(document.title + " - " + window.canonicalUrl),'twshare',726,436);
}

function googlePlusShare()
{    
    openShare('https://plusone.google.com/_/+1/confirm?url=' + encodeURIComponent(window.canonicalUrl), 'gshare', 500, 600);
}

function emailShare(username)
{
    openShare('/' + username + '/share?u=' + encodeURIComponent(window.canonicalUrl),'eshare',726,636);
}