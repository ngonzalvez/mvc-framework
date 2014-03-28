<?
/**
 *  Router.php
 * 
 *  This class manages all the routing process from the request to the controller's
 *  method call.
 */
class Router
{
    private $cfg;
    private $load;
    
    function __construct($loader, $config) {
        $this->load = $loader;
        $this->cfg = $config;
    }
    
    /**
     * This method loads the requested controller, makes the method call and passes
     * the params array to it. It takes a request as a parameter in the form of 
     * an associative array as follows:
     * $req = [ 
     *      "controller" => "ControllerName", 
     *      "method" => "MethodName", 
     *      "params" => ["Param1", "Param2", "Param3"]
     * ]
     * 
     * If no controller is specified in the request, the default controller will
     * be loaded. Similarly, if no specific method is requested then the Index
     * method will be called.
     */
    public function handle($req) {
        $controller = $this->load->controller($req['controller']) ?: $this->load->controller($this->cfg->get("APP", "DEFAULT_CONTROLLER"));
        $method = method_exists($controller, $req['method']) ? $req['method'] : "index";
        
        call_user_func_array(array($controller, $method), $req['params']);
    }
}
?>