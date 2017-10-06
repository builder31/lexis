<?php
class Oneword extends CI_Controller {
    
    private $objectTable = "oneword_words";
    
    public $appLinks = array (
	"Word of the Day"	=> "/app/oneword/wotd"
	,"Add Word"		=> "/app/oneword/add"
	,"Dictionary"		=> "/app/oneword/dictionary"
	,"My Words"		=> "/app/oneword/mySubmissions/"
    );
    
    function __construct() {
	parent::__construct();
	# load scaffolding feature with reference to table name to simplify data entry
	# $this->load->scaffolding('oneword_words');
	$this->load->helper('url');
	$this->load->helper('form');
    }
        
    function index($offset = 0) {
        $data['title'] = "The Last Word";
	#Find method to change page title to update with application
        $data['heading'] = "I Know One Word You Don't Know!"; #revise
        $this->load->model('Oneword_Word');
	#iterate through first 20 objects in the database
        $this->db->order_by('word_id','desc');  	#explicit mention of word_id
        $query = $this->db->get($this->Oneword_Word->getTableName(),20,$offset);
        $wordlist; #initialize variable
        foreach ($query->result() as $row) {
	    /* Create isolated instance of CI SuperObject and associate with random named model
	      Data is then assigned to model and pushed to aray
	      Check to see if it is possible to destroy SuperObject after building Array as current code consumes much memory
	    */
	    $CI = & get_instance();
	    $rnd = 'w'.rand().'w';
	    $CI->load->model('Oneword_Word',$rnd);
	    $CI->$rnd->load($row->word_id);
	    $chosenWord = $CI->$rnd;
	    $chosenWord->load($row->word_id);
	    $wordlist[] = $chosenWord;
        }
        $data['words'] = $wordlist; #list of words
        $data['single_word'] = $this->Oneword_Word; #word object for function reference
        $this->load->view('oneword/oneword_mainPage',$data);
    }

    function view() {
        $this->load->model('Oneword_Word','singleton');
        $this->load->model('Language','empty_language');
        $data['title'] = "OneWord";
        $data['ddNames']=$this->db->get('tribe_names');
        $this->singleton->load($this->uri->segment(3));
        $this->empty_language->load($this->singleton->getLangId());
        $view_word = array("name" => 'single word');
        $view_word["word"] = $this->singleton;
        $view_word["language"] = $this->empty_language;
        $this->db->where('word_id',$this->uri->segment(3));
	$view_word["comments"] = $this->db->get('oneword_comments');
        $data['single_word'] = $view_word;
        $data['empty_lang'] = $this->empty_language;
        $this->load->view('oneword/oneword_viewItem',$data);
    }

    function add() {
        $data['title'] = "OneWord: Add a word";
        $data['heading'] ="What's the Word?";
        $data['ddNames']=$this->db->get('tribe_names');
       $this->load->view('oneword/oneword_addItem',$data);
    }

    function insert() {
        $this->load->model('Oneword_Word','Word');
	require_once(ABSPATH . WPINC . '/registration.php');
	$this->load->helper('string');
	$vals = $_POST;
	/* verify existing user account and if no account, create object with random password */
        if(!is_user_logged_in()) {
            if(!get_user_by_email($vals['email_address'])) {
                $vals['user_id'] = wp_create_user(random_string(),random_string(),$vals['email_address']);
            } else {
                $vals['user_id'] = get_user_by_email($vals['email_address'])->ID;
            }
        }
	$vals = array_slice($vals,1);
	$ins = $this->Word->save($vals);
        redirect('/oneword/view/'.$ins);
    }    

    function add_comment() {
	require_once(ABSPATH . WPINC . '/registration.php');
	$this->load->helper('string');
        $vals = $_POST;
        if($vals['user_id'] == null) {
            if(!get_user_by_email($vals['email_address'])) {
                $vals['user_id'] = wp_create_user(random_string(),random_string(),$vals['email_address']);
            } else {    $vals['user_id'] = get_user_by_email($vals['email_address'])->ID;       }
        }

	$ins = $this->db->insert('oneword_comments',$vals);
	redirect('/oneword/view/'.$vals['word_id']);
    }

    function search() {
        $data['ddNames'] = $this->db->get('tribe_names');
        $this->load->view('oneword/oneword_searchForm',$data);
    }
    function searchContent() {
        //parse searchTerm as POST var to both tables LIKE word for singleton or LIKE meaning for multiple word entry
        $this->db->where('lang_id',$_POST['lang_id']);
        $isSingleton = false;
        $searchResults;
        if(str_word_count($_POST['searchTerm']) < 2) {
            //find alternative syntax for LIKE query
            $this->db->where('lang_id',$_POST['lang_id']);
            $this->db->like('word',$_POST['searchTerm'],both);
            $searchResults = $this->db->get('words',1);
            $this->load->view('oneword/oneword_search_result_single',$searchResults);
        } else {
            
            //db query for LIKE = $searchResults
            //searchResults query loads into more expansive array
            //split array into worthwhile value and organize accordingly
        }
        
    }
    function wotd() {
	#Word of the Day
        $this->load->view('base/underconstruction');
    }

    function dictionary($language = null) {
	#dictionary by language
        $this->load->view('base/underconstruction');
    }

    function mySubmissions($user) {
	#itemized submissions by user
        $this->load->view('base/underconstruction');
    }
    
    function findHelpful($id) {
	#incremenet helpful column
        $this->load->view('base/underconstruction');
    }

}
?>
