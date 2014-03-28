<?
/**
 * The File class provides a simple interface for reading files.
 */
class File
{
    private $filepath;
    private $lines;
    private $linesCount;
    private $currentLineIndex;
    
    /**
     * Constructor.
     */
    public function __construct($filepath) {
        $this->currentLineIndex = 0;
        $this->filepath   = $filepath;
        $this->lines      = array();
        $this->linesCount = 0;
        
        $this->open();
    }
    
    /**
     * Reads the file and stores its content in an array.
     */
    private function open() {
        if (file_exists($this->filepath)) {
            $this->lines = file($this->filepath);
            $this->linesCount = count($this->lines);    
        }
    }
    
    /**
      * Returns an array containing the lines in the file.
      * 
      * @return     String[]       Array of lines in the file.
      */
    public function getContent() {
      return $this->lines;
    }
    
    /**
     * Returns a line of the file. The returned line is determined by the line
     * number passed as first parameter to the method. If a line number is not
     * specified, then it returns the line that is next to the last returned line.
     * 
     * @param   $lineNum    The line index of the line to be returned.
     * 
     * @return  String      The requested line of the file.
     */
    public function getLine($lineIndex = null) {
        $lineIndex  = !is_null($lineIndex) ? $lineIndex : $this->currentLineIndex;
        
        if ($this->lineExists($lineIndex)) {
            return $this->lines[$lineIndex];    
        } 
        return false;
    }
    
    /**
     * Inserts a new line at the given position.
     * 
     * If no line index is specified, the string passed as a first parameter will
     * be inserted at the end of the file.
     * 
     * @param   String      Line to be inserted.
     * @param   Int         The line index where the line will be inserted.
     */
    public function insertLine($newLine, $lineIndex = null) {
        $lineIndex = is_null($lineIndex) ? $this->linesCount : $lineIndex;
        $this->lines =  array_merge(
                            array_slice($this->lines, 0, $lineIndex, true),
                            array($newLine."\n"),
                            array_slice($this->lines, $lineIndex, $this->linesCount, true)
                        );
        $this->linesCount++;
    }
    
    /**
     * Replaces a line in the file for a new one.
     * 
     * @param   Int         Index of the line to be replaced.
     * @param   String      Line to be inserted.
     */
    public function replaceLine($lineIndex = null, $newLine = null) {
        if (!is_null($newLine) && !is_null($lineIndex)) {
            $this->deleteLine($lineIndex);
            $this->insertLine($newLine, $lineIndex);
        }
    }
    
    /**
     * Deletes a line in the file.
     * 
     * @param   Int     The index of the line to be deleted.
     */
    public function deleteLine($lineIndex = null) {
        if (!is_null($lineIndex) && $lineIndex < $this->linesCount) {
            unset($this->lines[$lineIndex]);
        }
    }
    
    /**
     * Deletes all the content in the file.
     */
    public function deleteContent() {
        $this->lines = array();
    }
    
    /**
     * Saves the content to the file.
     */
    public function save() {
        file_put_contents($this->filepath, $this->lines);
    }
    
    /**
     * Checks whether the line index is valid or not.
     * 
     * @param   $lineIndex      The line index.
     * 
     * @return  Boolean         Indicates whether the line index is valid or not.
     */
    private function lineExists($lineIndex) {
        return $lineIndex >= 0 && $lineIndex < $this->linesCount;
    }
}
?>