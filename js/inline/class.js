function Class() {}
function makeClass($base, $protoProps, $classProps)
{
    $base = $base || Class;
    var $pproto = $base.prototype,
        $class = function() { this.init.apply(this, arguments); },
        $proto = function() { this._pproto = $pproto; };    
    $proto.prototype = $pproto;
    $class.prototype = new $proto;
    
    if ($protoProps)
    {
        extend($class.prototype, $protoProps);
    }
    if ($classProps)
    {
        extend($class, $classProps);
    }
    
    return $class;
}

function extend(to, from)
{
    for (var name in from)
    {
        to[name] = from[name];
    }
    return to;
}
