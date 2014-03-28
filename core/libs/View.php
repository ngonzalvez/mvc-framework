<?
class View
{
    private $data;
    private $view_name;
    private $loader;
    
    public function __construct($loader, $view_name, $data = array()) {
        $this->data = $data;
        $this->view_name = $view_name;
        $this->loader = $loader;
    }
    
    public function __get($name) {
        echo "Variable '$name' requested";
        return $this->data[$name];
    }
    
    public function render() {
        $this->loader->load("views/".$this->view_name.".php");
    }
}
?>