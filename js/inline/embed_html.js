var embedTrustedHTML = (function() {
    var callCount = 0;

    return function(container, html)
    {
        var scripts = [];
        var prefix = "SCRIPT_PLACEHOLDER_" + callCount + "_";                
        callCount++;       

        // setting innerHTML doesn't execute scripts, 
        // so we create manual script elements for each script tag found in the HTML        
     
        html = html.replace(/<script([^>]*)>([\s\S]*?)<\/script>/ig, function(match, attrs, text) {
        
            var index = scripts.length;
            
            var script = document.createElement('script');
            script.text = text;
            script.type = 'text/javascript';                   
            
            // external scripts don't quite work with this method because they are not 
            // guaranteed to load in order.
            
            /*                      
            var match = attrs.match(/src=["']([^"']*)["']/);
            if (match)
            {
                // assumed no html encoded characters in src url
                script.src = match[1];
            }     
            */            
            scripts.push(script);        
            return "<span id='"+prefix+index+"'></span>";
        });
            
        container.innerHTML = html;
            
        for (var i = 0; i < scripts.length; i++)
        {        
            var span = $(prefix + i);
            span.appendChild(scripts[i]);    
        }
    }
})();

function addLocalizedStrings(strs)
{
    for (var str in strs)
    {
        jsStrs[str] = strs[str];
    }
}