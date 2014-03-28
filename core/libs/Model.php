<?
class Model
{
    protected $db;
    
    public function __construct() {
        $this->db = new Database(Loader::Instance(), Config::Instance());
    }
    
    public function connect() {
        $this->db->connect();
    }
    
    public function disconnect() {
        $this->db->disconnect();
    }
}
?>