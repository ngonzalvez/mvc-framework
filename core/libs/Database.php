<?
/**
 * This class provides abstraction for DB connection handling and querying.
 * 
 * The database server MUST be MySQL.
 */
class Database 
{
    private $connection;
    private $config;
    private $table;
    private $conditions;
    private $result;
    
    public function __construct($loader, $config) {
        $this->config = $config;
        $this->connection = null;
        
        $load = $loader;
        $load->helper("isAssoc");
    }
    
    /**
     * Creates a new connection with the database.
     */
    public function connect() {
        if ($this->connection === null) {
            $this->connection = new mysqli(
                    $this->config->get("DB", "SERVER"),
                    $this->config->get("DB", "USERNAME"),
                    $this->config->get("DB", "PASSWORD"),
                    $this->config->get("DB", "DATABASE")
                );
            $this->setCharset('utf8');
                
            if ($this->connection->connect_error) {
                throw new Exception("Couldn't connect to the database");
            }
        }
    }
    
    /**
     * Closes the connection with the database.
     */
    public function disconnect() {
        if ($this->connection !== null) {
            $this->connection->close();
        }
    }

    /**
      * Sets a new charset.
      *
      * @method  setCharset
      * 
      * @param   String   $charset    The charset to be used in the DB connection.
      */
    public function setCharset($charset) {
        $this->connection->set_charset($charset);
    }
    
    /**
     * Sends a query to the database.
     *
     * @method  query
     * 
     * @param   $query      SQL query.
     */
    private function query($mainQuery = '') {
        $where = empty($this->conditions) ? '' : 'WHERE '. implode(',', $this->conditions);
        $limit = '';
        $orderBy = '';
        echo "$mainQuery $where $limit $orderBy";
        $this->result = $this->connection->query("$mainQuery $where $limit $orderBy");
        $this->emptySQL();
        return $this;
    }

    /**
     * Deletes all the previous settings.
     *
     * @method   emptySQL
     */
    private function emptySQL() {
        $this->conditions = array();
        $this->limit    = null;
        $this->from     = null;
        $this->orderBy  = null;
        $this->table    = null;
    }
    
    /**
     * Creates a new table.
     *
     * @method  createTable
     * 
     * @param   $table      The name of the new table.
     * @param   $fields     An associative array of fields => types.
     */
    public function createTable($table, $fields) {
        array_walk($fields, function(&$val, $key) { $val = "$key $val"; });
        $fields = implode(",", $fields);
        
        $this->query("CREATE TABLE $table($fields)");
        return $this;
    }
    
    /**
     * Creates a new database.
     *
     * @method  createDatabase
     * 
     * @param   $db_name    The name of the database.
     */
    public function createDatabase($db_name) {
        $this->query("CREATE DATABASE $db_name");
        return $this;
    }
    
    /**
     * Inserts a new row in the table.
     *
     * @method  insert
     * 
     * @param   $table      The name of the table in which the row will be added.
     * @param   $row        The data to be added.
     */
    public function insert($table, $row) {
        $fields = isAssoc($row) ? "'" .implode("','", array_keys($row)). "'" : '';
        $values = "'" . implode("','", array_values($row)) . "'";
        $this->query("INSERT INTO $table ($fields) VALUES ($values)");
        return $this;
    }

    /**
     * Sets a table in which the queries will be performed.
     *
     * @method  from
     *
     * @param   String    $table   The name of the table
     *
     * @return  Database           An instance of the database object.
     */
    public function from($table) {
        $this->table = $table;
        return $this;
    }

    /**
     * Sets a new condition for the row to be inserted.
     *
     * @method where
     * 
     * @param  String   $field    The name of the field
     * @param  String   $value    The value of the field.
     * 
     * @return Database           An instance of the database object.
     */
    public function where($field, $value = null) {
        if (is_null($value) and is_array($field)) {
            foreach($field as $key => $val) {
                $this->where($key, $val);
            }
        } 
        else {
            $this->conditions[] = "$field='$value'";
        }
        return $this;
    }

    /**
     * Updates the value of the given fields.
     *
     * @method update
     * 
     * @param  String[] $row    Associative array of $field => $value.
     * 
     * @return Database         An instance of the database object.
     */
    public function update($row) {
        array_walk($row, function(&$val, $field) { $val = "$field='$val'"; });
        $row = implode(',', $row);
        $this->query("UPDATE {$this->table} SET $row");
        return $this;
    }

    /**
     * Deletes a row in the table.
     *
     * @method   delete
     *
     * @return   Database   An instance of the database object.
     */
    public function delete() {
        $this->query("DELETE FROM {$this->table}");
        return $this;
    }

    /**
     * Select the given fields from the current table.
     *
     * @method   select
     *
     * @param    String     $fields   An array of fields or a string.
     *
     * @return   Database             An instance of the database object.
     */
    public function select($fields = '*') {
        if (is_array($fields)) {
            $fields = implode(',', $fields);
        }

        $this->query("SELECT $fields FROM {$this->table}");
        return $this;
    }


    /**
     * Returns an array of rows where each row is represented by a associative 
     * array.
     *
     * @method   result_array
     *
     * @return   String[][]   An array of rows.
     */
    public function result_array() {
        return $this->result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Returns an array of row where every row is represented by an object.
     *
     * @method   result
     * 
     * @return   Object[]   An array of rows.
     */
    public function result() {
        $result = [];
        while($row = $this->row()) {
            $result[] = $row;
        }
        return $result;
    }

    /**
     * Returns an object representing a row.
     *
     * @method   row
     *
     * @return   Object   A row in the result.
     */
    public function row() {
        if ($this->result) {
            return $this->result->fetch_object();
        }
    }


    /**
     * Returns a row as an associative array.
     *
     * @method   row_array
     *
     * @return   String[]   A row in the result.
     */
    public function row_array() {
        return $this->result->fetch_array(MYSQLI_ASSOC);
    }
}


?>