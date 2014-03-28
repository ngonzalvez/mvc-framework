<?
final class Config 
{
    static private $inst = null;
    private $cfg;
    
    private function __construct() {
        // Loads the main framework config.
        $this->load(CORE_PATH."/config/SystemConfig.ini");
        
        // Relative path to the app folder
        $app_path = $this->get("APP","PATH");
        
        // Loads the app specific configuration.
        $this->load($app_path."/config/App.ini");
        $this->load($app_path."/config/Database.ini");
    }
    
    /**
     * Always returns the same instance of the class.
     */
    public static function Instance() {
        self::$inst = self::$inst ?: new Config();
        return self::$inst;
    }
    
    /**
     * Loads and initializes the config file.
     */
    private function load($filepath) {
        $ini_array = parse_ini_file($filepath, true);
        
        foreach ($ini_array as $section => $properties) {
            foreach ($properties as $property => $value) {
                $this->cfg[$section][$property] = $value;
            } 
        }
    }
    
    /**
     * Returns the value of the requested property. It takes two arguments, the
     * first specifies the configuration section in which the property is located
     * and the seccond specifies the property name.
     */
    public function get($section, $property) {
        return $this->cfg[$section][$property] ?: null;
    }
    
}
?>