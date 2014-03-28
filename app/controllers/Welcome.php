<?

class Welcome extends Controller
{
    /**
     * The index method will be called in case no other specific method was specified
     * in the URL request.
     */
    public function index() {
        // Some data.
        $data = array(
                    "firstname" => "Cristopher",
                    "lastname"  => "Gonzálvez"
                );

        // Loads the view and pass the data to it. The data wil be loaded into the
        // view where every key in the array will be a variable, in this case: 
        // $firstname and $lastname.
        $this->load->view("Welcome", $data);
    }   
}

?>