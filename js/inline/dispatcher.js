var Dispatcher = makeClass();
extend(Dispatcher.prototype, {
    init: function() 
    {
        this.clearListeners();
    },
    
    addListener: function(fn) 
    {
        this.listeners.push(fn);
    },
    
    addListenerOnce: function(fn)
    {
        this.listenersOnce.push(fn);
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
            this.listenersOnce[i].apply(this, arguments);
        }        
        this.listenersOnce = [];

        for (var i = 0; i < this.listeners.length; i++)
        {
            this.listeners[i].apply(this, arguments);
        }
    }
});
