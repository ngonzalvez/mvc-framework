<?
class CSVFile
{
    private $file;
    private $delimiter;
    private $data;
    
    /**
     * Constructor.
     */
    public function __construct($filepath, $delimiter = ',') {
        $this->file = new File($filepath);
        $this->delimiter = $delimiter;
        $this->parseData();
    }
    
    /**
     * Parses the file and gets the table of data.
     * 
     * The data table is retrieved as a multidimensional array with its first
     * dimension representing the rows, and the second dimension representing
     * the cells in that row (that is, the columns).
     * 
     * @return  String[][]      The data table with the values in the CSV file.
     */
    private function parseData() {
        $this->data = array();
        $lines      = $this->file->getContent();
        
        foreach ($lines as $line) {
            $this->data[] = explode($this->delimiter, $line);
        }
    }
    
    /**
     * Returns the data table.
     * 
     * @return  String[][]  The data table in the file.
     */
    public function getData() {
        return $this->data;
    }
    
    /**
     * Retrieves a row by its index.
     *
     * @param   Int         The index of the row to be returned.
     * 
     * @return  String[]    Array of values in the row.
     */
    public function getRow($row = null) {
        if (is_null($row)) {
            return false;   
        }
        return $this->data[$row];
    }
    
    
    /**
     * Returns an array containing the cells in a specific column.
     * 
     * @param   $column     The index of the column to be returned.
     * 
     * @return  String[]    The values in the column.
     */
    public function getColumn($column = null) {
        if (!is_null($column)) {
            $cells = array();
            foreach ($this->data as $row) {
                (count($row) > $column) and ($cells[] = $row[$column]);
            }
            return $cells;
        }
    }
    
    /**
     * Retrieves a cell by its row index and column index.
     * 
     * @param   $row    The row to which the cell belongs.
     * @param   $col    The column in which the cell is located.
     * 
     * @return  String  The content of the cell.
     */
    public function getCell($row = null, $col = null) {
        if ($this->isValidCell($row, $column)) {
            return $this->data[$row][$column];
        }
    }
    
    /**
     * Updates the value of a cell in the data table.
     *
     * @param   $row        The index of the row in which the cell is located.
     * @param   $col        The index of the column in which the cell is located.
     * @param   $value      The new value of the cell.
     */
    public function updateCell($row = null, $col = null, $value = null) {
        if ($this->isValidCell($row, $col) and !is_null($value)) {
            $this->data[$row][$col] = $value;
        }
    }
    
    /**
     * Returns TRUE if the given position in the table exists.
     * 
     * @param   $row        The index of the row.
     * @param   $col        The index of the column.
     * 
     * @return  Boolean     Whether the cell exists or not.
     */
    private function isValidCell($row = null, $columnIndex = null) {
        if (!is_null($row) && !is_null($columnIndex)) {
            $numberOfColumns = count($this->data[$row]);
            return $numberOfColumns > $columnIndex;
        }
    }
    
    /**
     * Saves the data to the CSV file.
     */
    public function save() {
        $this->file->deleteContent();
        foreach ($this->data as $row) {
            $this->file->insertLine(implode($this->delimiter, $row));
        }
        $this->file->save();
    }
}
?>