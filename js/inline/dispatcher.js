var Dispatcher = makeClass();
extend(Dispatcher.prototype, {
    init: function() 
    {
        this.clearListeners();
    },
    
    addListener: function(fn, thisArg) 
    {
        this.listeners.push({fn:fn,thisArg:thisArg});
    },
    
    addListenerOnce: function(fn, thisArg)
    {
        this.listenersOnce.push({fn:fn,thisArg:thisArg});
    },

    clearListeners: function()
    {
        this.listeners = [];
        this.listenersOnce = [];
    },
    
    dispatch: function(/* arguments*/)
    {
        for (var i = 0; i < this.listenersOnce.length; i++)
        {            
            var listener = this.listenersOnce[i];
            listener.fn.apply(listener.thisArg || this, arguments);
        }        
        this.listenersOnce = [];

        for (var i = 0; i < this.listeners.length; i++)
        {
            var listener = this.listeners[i];
            listener.fn.apply(listener.thisArg || this, arguments);
        }
    }
});
