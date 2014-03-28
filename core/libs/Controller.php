<?
/**
 * Base class that every controller in the app will extend.
 * 
 * It provides a loader for loading views, helpers and models and an abstract
 * index method that every subclass must overwrite.
 */
abstract class Controller {
    protected $load;
    private static $instance;
    
    public function __construct() {
        // Loader for loading views, helpers and models.
        $this->load = Loader::Instance();
        
        // A static reference to the controller.
        self::$instance =& $this;
    }
    
    /**
     * Returns a reference to $this.
     * 
     * A reference to the controller object is needed by the Loader for the model
     * loading process.
     * 
     * @static
     * 
     * @return  Controller      A reference to the current controller object.
     */
    public static function &Instance() {
        return self::$instance;
    }
    
    /**
     * The index method will be called if no other method was requested for the
     * controller.
     */
    abstract public function index();
}

?>