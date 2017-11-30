<?php
class Dict_Word extends CI_Model {
    #Configurable Table names
    private $objectTable = "oneword_words";
    private $languageTable = "dict_languages";
    #Dynamic private variables for object
    private $priIdxKey = "word_id";
    private $word_id;           //int(5) auto_increment index primary key
    private $author_id;           //varchar(200) foreign key index
    private $word;              //varchar(100)
    private $meaning;           //text
    private $lang_id;           //int(5) foreign key index
    private $posted;            //datetime
    public  $active;             //int(1)
    public  $foundHelpful;       //int(5)

    function __construct() {
        parent::__construct();
    }

    //accessor methods
    public function getObjectTableName()    { return $this->objectTable; }
    public function getAuthorTableName()    { return $this->authorTable; }
    public function getIdxCol()             { return $this->priIdxKey;  }
    public function getWordId()             { return $this->word_id; }
    public function getAuthorId()           { return $this->author_id; }
    public function getWord()               { return $this->word; }
    public function getMeaning()            { return $this->meaning; }
    public function getLangId()             { return $this->lang_id; }
    public function getPosted()             { return $this->posted; }

    //mutator methods
    public function setAuthorId($z) { $this->author_id = $z; }
    public function setWord($z)     { $this->word = $z; }
    public function setMeaning($z)  { $this->meaning = $z; }
    private function setLangId($z)  { $this->lang_id = $z; }
    public function setPosted($z)   { $this->posted = $z; }

    //crud methods
    public function save($data = null) {
        if(isset($data)) { $this->db->insert($this->object_table,$data); }
        else { $this->db->insert($this->object_table,$this); }
        return $this->db->insert_id();
    }
    public function update($id,$z = null)     {
        $this->db->where($this->priIdxKey,$id);
        if($z == null) { $this->db->update($this->object_table,$this); }
        else { $this->db->update($this->object_table, $z); }
        }
    public function load($id = null)    { #function explicitly mentions column name
        if($id == null) { $this->db->where($this->priIdxKey,$this->word_id); }
        else { $this->db->where($this->priIdxKey,$id); }
        $query = $this->db->get($this->objectTable);
        foreach ($query->result() as $row) {
            $this->word_id = $row->word_id;
            $this->author_id = $row->author_id;
            $this->word = $row->word;
            $this->meaning = $row->meaning;
            $this->lang_id = $row->lang_id;
            $this->posted = $row->posted;
            $this->active = $row->active;
            $this->foundHelpful = $row->foundHelpful;
        }
    }
    public function delete($id) {
        $this->db->where('word_id', $id);   #function explicitly mentions column name
        $this->db->delete($this->object_table);
    }
    
    //other methods
    public function count_all() { return $this->db->count_all($this->objectTable); }
    public function getRandom() {
        $sql = "SELECT ".$this->priIdxKey." FROM ".$this->object_table." ORDER BY RAND() LIMIT 1";
        $result = mysql_query($sql) or die("Problem obtaining record. ".mysql_error());
         return $this->word_id = mysql_result($result,0);
    }
    public function get_paged_list($limit=10,$offset=0,$order='desc') {
        #iterate through objects in the database as set by limit and offset
        $this->db->order_by($this->priIdxKey,$order);
        $wList; #init variable to hold results from query
        $q=$this->db->get($this->objectTable,$limit,$offset);
        foreach ($q->result() as $row) {
            /* Create isolated instance of CI SuperObject and associate with random named model
              Data is then assigned to model and pushed to array
              Check to see if possible to destroy SuperObject after building array
              possibly memory heavy if growing too large */
            $CI = & get_instance();
	    $rnd = 'w'.rand().'w';
	    $CI->load->model($this,$rnd);
	    $CI->$rnd->load($row->$this->priIdxKey);
	    $wList[] = $CI->$rnd;            
        }
        return $wList;
        #return $this->db->get($this->object_table,$limit,$offset);
    }

    public function get_by_id($id) {
        $this->where($this->priIdxKey,$id);
        return $this->db->get($this->object_table);
    }    

   }
?>
