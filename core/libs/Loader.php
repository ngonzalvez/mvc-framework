<?
/**
 * Abstraction layer for loading libraries, controllers, models, helpers and 
 * other components of the framework.
 */
final class Loader
{
    static private $inst;
    private $cfg;
    
    private function __construct() {
    }
    
    /**
     * Always returns the same instance of the Loader singleton.
     */
    public static function Instance() {
        self::$inst = self::$inst ?: new Loader();
        return self::$inst;
    }
    
    /**
     * Sets the instance for the configuration abstraction layer.
     */
    public function setConfig($cfg) {
        $this->cfg = $cfg;
    }
    
    /**
     * Loads a library by its name and instantiate it.
     * 
     * @param   $library       The name of the library
     * 
     * @return  bool|Object    Returns an instance of the library or false if it could not be loaded.
     */
    public function library($library, $libraryName = null) {
        $libraryName = $libraryName ?: $library;
        
        if ($this->load("libs/$library.php")) {
            if ($controller =& Controller::Instance()) {
               $controller->{$libraryName} = new $library();
            }
        }
        return false;
    }
    
    /**
     * Loads a controller by its name and instantiate it.
     * 
     * @param   $controller     The name of the controller.
     * 
     * @return  Controller      An instance of the controller.
     */
    public function controller($controller) {
        $controller = ucfirst($controller);
        if ($this->load("controllers/$controller.php")) {
            return new $controller;
        }
    }
    
    /**
     * Loads a model and instanciate it.
     * 
     * @param   $model          The name of the model.
     * @param   $autoconnect    Boolean value indicating whether autoconnection should be enabled or not.
     */
    public function model($model, $autoconnect = false, $modelName = null) {
        $modelName = $modelName ?: $model;
        
        if ($this->load("models/$model.php")) {
            // Gets a reference of the last instanciated controller.
            if ($controller =& Controller::Instance()) {
               $controller->{$modelName} = new $model();
               
               if ($autoconnect) {
                   $controller->{$modelName}->connect();
               }
            }
        }
     }
    
    /**
     * Loads a helper function by its name.
     * 
     * @param   $helper     The name of the helper.
     * 
     * @return  bool        Indicates whether the loading was successfull or not.
     */
    public function helper($helper) {
        return $this->load("helpers/$helper.php");
    }
    
    /**
     * Loads a view by its name.
     * 
     * @param   $view       The name of the view.
     * @param   $data       An associative array with the data to be passed to the view.
     */
     public function view($view, $data = array()) {
            if (file_exists($this->cfg->get('APP','PATH')."views/$view.php")) {
                // Transforms the data array into independent variables.
                extract($data);
                // Loads the view
                include($this->cfg->get('APP','PATH')."views/$view.php");
          }
     }
     
    /**
     * Loads the requested file from the app folder if found and from the core
     * folder otherwise. This allows the users to overwrite the default helpers
     * and core classes with their owns.
     * 
     * @param   $file_path      Relative path to the file.
     * 
     * @return  bool            Indicates whether the loading was successfull or not.
     */
    public function load($file_path) {
        try {
            if (file_exists($this->cfg->get('APP','PATH').$file_path)) {
                include_once($this->cfg->get('APP','PATH').$file_path);
            }
            else if (file_exists($this->cfg->get('CORE','PATH').$file_path)) {
                include_once($this->cfg->get('CORE','PATH').$file_path);
            }
            else {
                throw new Exception('File not found');
            }
        } catch (Exception $e) {
            return false;
        }
        return true;
    }
}

?>