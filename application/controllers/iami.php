<?php

class Iami extends CI_Controller {
    
    function __construct() {
        parent::__construct();
        //load scaffolding feature with reference to table name to simplify data entry
        //$this->load->scaffolding('iami_responses');
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->model('SimpleDBTagCloud');
        $this->load->model('TagCollector');
    }
    
    function index() {
        
    $data['title'] = "I am Injun";
    $data['heading'] = "What makes you proud of your native heritage?";
    $data['ddNames']=$this->db->get('tribe_names');
    $data['query'] = $this->db->get('iami_responses');
    $this->load->view('iami/iami_view',$data);
    
    }
    
    function browse() {
        echo 'this is my list'; 
    }
    function view() {
    $data['title'] = "I am Injun";
    $data['heading'] = "What makes you proud of your native heritage?";
        $data['ddNames']=$this->db->get('tribe_names');
    $this->db->where('response_id',$this->uri->segment(3));
        $data['query'] = $this->db->get('iami_responses');

    $this->load->view('iami/iami_view',$data);
        
    }
    
    function comments() {
    $data['title'] = "My Comment Title";
    $data['heading'] = "My Comment Heading";
    $this->db->where('entry_id',$this->uri->segment(3));
        $data['query'] = $this->db->get('comments_ci');

    $this->load->view('comment_view',$data);
    }
        
    function iami_insert() {
        $this->db->insert('iami_responses',$_POST);
        $jin=$this->db->insert_id();

        $tagFinder = new TagCollector();
/*        $tagFinder->setDBHost("localhost");
        $tagFinder->setDBName("nativestrength");
        $tagFinder->setDBUserName("root");
        $tagFinder->setDBUserPass("root");*/
        $tagFinder->tagTable="tags";
//        $tagFinder->connect();        
        
        $tagFinder->openDBDataSource("iami_responses","contents",$jin-1,1);
        redirect('app/index.php/iami/iami/view/'.$jin);
    }
    
}
?>