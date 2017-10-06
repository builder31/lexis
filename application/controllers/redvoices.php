<?php

class RedVoices extends CI_Controller {
    
    public $appLinks = array (
	"My Submissions" => "/app/redvoices/mySubmissions/"
    );
    
    function __construct() {
	parent::__construct();
	# load scaffolding feature with reference to table name to simplify data entry
	# $this->load->scaffolding('oneword_words');
	$this->load->helper('url');
	$this->load->helper('form');
	$this->load->model('App_User');
    }
        
    function index($offset = 0) {
    /* Pagination will still need to be added. This should be a limit of 20 items per page
      The pagination link itself should exist at the bottom of the page
    */
        $data['title'] = "OneWord";
        $data['heading'] = "I Know One Word You Don't Know!"; #revise
        $this->load->model('Oneword_Word'); //iterate through object array
        $this->db->order_by('word_id','desc');  #explicit mention of word_id
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
        $data['words'] = $wordlist;
        $data['single_word'] = $this->Oneword_Word;
	$this->load->view('base/header');
        $this->load->view('oneword/oneword_mainPage',$data);
	$this->load->view('base/sidebar');
	$this->load->view('base/footer');
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
	$this->load->view('base/header');
        $this->load->view('oneword/oneword_viewItem',$data);
	$this->load->view('base/sidebar');
	$this->load->view('base/footer');
    }

    function add() {
        $data['title'] = "OneWord: Add a word";
        $data['heading'] ="What's the Word?";
        $data['ddNames']=$this->db->get('tribe_names');
	$this->load->view('base/header');
        $this->load->view('oneword/oneword_addItem',$data);
	$this->load->view('base/sidebar');
	$this->load->view('base/footer');
    }

    function insert() {
        $this->load->model('Oneword_Word','Word');
	$this->load->helper('string');
	$vals = $_POST;
	/* verify existing user account and if no account, create object with random password */
	if($this->App_User->validateUser($_POST['email'])) {
	    $vals['user_id'] = $this->App_User->findByEmail($_POST['email']);
	} else {
	    $vals['user_id'] = $this->App_User->save(array('user_id' => 0,'email' => $_POST['email']));
	}
	$vals = array_slice($vals,1);
	$ins = $this->Word->save($vals);
        redirect('/oneword/view/'.$ins);
    }

    function add_comment() {
	$this->load->helper('string');
	$vals = $_POST;
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
    function mySubmissions($user) {
	#itemized submissions by user
	$this->load->view('base/header');
	echo 'this section is still being actively coded';
	$this->load->view('base/sidebar');
	$this->load->view('base/footer');
    }
        
}
?>
