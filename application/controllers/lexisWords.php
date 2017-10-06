<?php
class LexisWords extends CI_Controller {
    
    public $appURI = 'lexisWords/';    
    public $appLinks = array (
	#remember to add base_url().$appURI function before printing member values
	"Main"			=> 'index',
	"Word of the Day"	=> 'wotd/'
	,"Add Word"		=> 'add/'
	,"Dictionary"		=> 'lexisWords/'
	#,"My Words"		=> 'mySubmissions/'
    );

    function __construct() {
	parent::__construct();
	# load scaffolding feature with reference to table name to simplify data entry
	# $this->load->scaffolding('oneword_words');
	$this->load->helper('form');
    }
    function drawLinks() {
	$s;
	foreach($this->appLinks as $linkName => $link) {
	    $s =$s." [".anchor(base_url().$this->appURI.$link,$linkName)."]";
	}
	return $s;
    }
    function index($offset = 0) {
	#pagination will become a need in the future
	#Find method to change page title to update with application
        $this->load->model('LexisWord');
	$idx = $this->LexisWord->getIdxCol();
	#iterate through first 20 objects in the database
        $query = $this->db->get($this->LexisWord->getObjectTableName(),20,$offset);
        $wordlist; #initialize variable
	foreach($query->result() as $word) {
	    $chosenWord = 'w'.rand().'w'; #each object given distint name by random assignment
	    $this->load->model('LexisWord',$chosenWord);
	    $this->$chosenWord->load($word->$idx);
	    $wordlist[] = $this->$chosenWord;	#store object/model to array
	}
	$data['links']=$this->drawLinks();
	$data['words'] = $wordlist; #list of words
	$this->load->view($this->appURI.'lexisWords',$data);
    }

    function view() {
        $this->load->model('LexisWord','singleton');
        $data['ddNames']=$this->db->get('lexis_languages');
        $this->singleton->load($this->uri->segment(3));
        $this->db->where('word_id',$this->uri->segment(3));
	$data['comments'] = $this->db->get('oneword_comments');
        $data['single_word'] = $this->singleton;
	$data['links'] = $this->drawLinks();
        $this->load->view($this->appURI.'oneword_viewItem',$data);
    }

    function add() {
	$data['ddNames']=$this->db->get('lexis_languages');
        $this->load->view($this->appURI.'oneword_addItem',$data);
    }

    function insert() {
        $this->load->model('LexisWord','Word');
	$this->load->helper('string');
	$vals = $_POST;
	/* verify existing user account and if no account, create object with random password */
	$vals = array_slice($vals,1);
	$ins = $this->Word->save($vals);
        redirect($this->appURI.'view/'.$ins);
    }    

    function add_comment() {
	$this->load->helper('string');
        $vals = $_POST;
	$ins = $this->db->insert('oneword_comments',$vals);
	redirect($this->appURI.'view/'.$vals['word_id']);
    }

    function search() {
        #You will need to find a point of integration for this with the site-wide search presented by wordpress. For this reason, it may be worthwhile to identify your content as a custom content type and/or write your own blog import software
        $data['ddNames'] = $this->db->get('lexis_languages');
        $this->load->view($this->appURI.'oneword_searchForm',$data);
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
	    $this->load->view($this->appURI.'oneword_Search_result_single',$searchResults);
        } else {
            
            //db query for LIKE = $searchResults
            //searchResults query loads into more expansive array
            //split array into worthwhile value and organize accordingly
        }
        
    }
    function wotd() {
	#Word of the Day
	$this->load->model('LexisWord');
	$maxNum=$this->LexisWord->count_all();
	$this->LexisWord->load(rand(1,$maxNum));
	$data['single_word'] = $this->Dict_Word;
	$data['comments'] = $this->db->get_where('oneword_comments',array($this->Dict_Word->getIdxCol() => $this->Dict_Word->getWordId()));
	$data['links'] = $this->drawLinks();
        $this->load->view($this->appURI.'oneword_viewItem',$data);
	#$this->load->view('general/underconstruction');
    }

    function dictionary($language = null) {
	$this->load->model('Dict_Word');
	$this->load->model('language');
	#dictionary by language
	$data; #empty object to send array data
	$numLanguages = $this->db->count_all($this->Dict_Word->getLanguageTableName());
	if($language != null) {
	    #display words in language
	    $id=$this->uri->segment(3);
	    if(($id > -1) || ($id < $numLanguages)) {
		$query = $this->db->get_where($this->Dict_Word->getObjectTableName(),array('lang_id' => $id));
		$wordlist; #init variable
		foreach($query->result() as $word) {
		    $chosenWord = 'w'.rand().'w'; #each object given distint name by random assignment
		    $this->load->model('Dict_Word',$chosenWord);
		    $this->$chosenWord->load($word->$idx);
		    $wordlist[] = $this->$chosenWord;	#store object/model to array
		}
		$data['words'] = $wordlist;
	    } else { print "Invalid Language ID"; }
	} else {
	    #display list of langauges
	    $query = $this->db->get($this->Dict_word->getLanguageTableName());
	    $data['language_list'] = $query->result();
	}
	$this->load->view('lexis/dictionary',$data);
        $this->load->view('general/underconstruction');
    }

    function mySubmissions($user) {
	#itemized submissions by user
        $this->load->view('general/underconstruction');
    }

}
?>
