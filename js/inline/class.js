function Class() {}
function makeClass($base)
{
    $base = $base || Class;
    var $class = function() { this.init.apply(this, arguments); };
    var $proto = function() {};
    $proto.prototype = $base.prototype;
    $class.prototype = new $proto;
    return $class;
}
