<?php
class lexisword extends CI_Model {
        #Configurable Table names
    private $objectTable = "lexis_words";
    private $languageTable = "lexis_languages";
    #Dynamic private variables for object
    private $priIdxKey;         /* = "word_id"*/
    private $word_id;           //int(5) auto_increment index primary key
    private $user_id;           //varchar(200) foreign key index
    private $word;              //varchar(100)
    private $meaning;           //text
    private $lang_id;           //int(5) foreign key index
    private $posted;            //datetime
    public  $active;            //int(1)
    public  $foundHelpful;      //int(5)

    function __construct() {
        parent::__construct();
        $this->priIdxKey = $this->getIdxCol();
    }
    //accessor methods
    public function getUserId()     { return $this->user_id; }
    public function getWordId()     { return $this->word_id; }
    public function getWord()       { return $this->word; }
    public function getMeaning()    { return $this->meaning; }
    public function getLangId()     { return $this->lang_id; }
    public function getPosted()     { return $this->posted; }
    public function getObjectTableName()    { return $this->objectTable; }
    public function getLanguageTableName()    { return $this->languageTable; }
    public function getIdxCol() {
        $this->getPrimaryKey();
        return $this->priIdxKey;
    }
    
    public function getLanguage($q= null) {
        if($q == null) {
            $query=$this->db->get_where($this->languageTable,array('lang_id' => $this->lang_id),1)->result();}
        else { $query=$this->db->get_where($this->languageTable,array('lang_id'=>$q),1)->result(); }
        return $query[0]->lang_name;
    }

    //mutator methods
    public function setWordId($z)   { $this->word_id = $z;    }
    public function setUserId($z)   { $this->user_id = $z; }
    public function setWord($z)     { $this->word = $z; }
    public function setMeaning($z)  { $this->meaning = $z; }
    private function setLangId($z)  { $this->lang_id = $z; }
    public function setPosted($z)   { $this->posted = $z; }    
    
        //db methods
    public function count_all() { return $this->db->count_all($this->object_table); }
    //admin control methods
    public function get_paged_list($limit=10,$offset=0) {
        $this->db->order_by('word_id','asc');
        return $this->db->get($this->object_table,$limit,$offset);
    }
    //crud methods
    public function save($data = null) {
        if(isset($data)) { $this->db->insert($this->object_table,$data); }
        else { $this->db->insert($this->object_table,$this); }
        return $this->db->insert_id();
    }
    public function delete($id) {
        $this->db->where($this->priIdxKey, $id);   #function explicitly mentions column name
        $this->db->delete($this->object_table);
    }
    public function update($id,$z = null)     {
        $this->db->where($this->priIdxKey,$id);
        if($z == null) { $this->db->update($this->object_table,$this); }
        else { $this->db->update($this->object_table, $z); }
        }
    public function load($id = null)    { #function explicitly mentions column name
        if($id == null) { $this->db->where($this->getIdxCol(),$this->word_id); }
        else { $this->db->where($this->getIdxCol(),$id); }
        $query = $this->db->get($this->objectTable);
        foreach ($query->result() as $row) {
            $this->word_id = $row->word_id;
            $this->user_id = $row->user_id;
            $this->word = $row->word;
            $this->meaning = $row->meaning;
            $this->lang_id = $row->lang_id;
            $this->posted = $row->posted;
            $this->active = $row->active;
            $this->foundHelpful = $row->foundHelpful;
        }
    }
    
    //other methods
    function getPrimaryKey() {
        $fields = $this->db->field_data($this->objectTable);
        #set first field name as primary key
        $this->priIdxKey = $fields[0]->name;
        foreach ($fields as $field) {
            if($field->primary_key > 0) {
                #update if another field is the primary key
                $this->priIdxKey = $field->name;
            }
        }
    }
    public function getRandom() {
        $sql = "SELECT ".$this->priIdxKey." FROM ".$this->object_table." ORDER BY RAND() LIMIT 1";
        $result = mysql_query($sql) or die("Problem obtaining record. ".mysql_error());
         return $this->word_id = mysql_result($result,0);
    }
    public function get_by_id($id) {
        $this->where($this->priIdxKey,$id);
        return $this->db->get($this->object_table);
    }
    public function getLatest() {
        $sql = "SELECT ".$this->priIdxKey." FROM ".$this->objectTable." ORDER BY ".$this->priIdxKey. "DESC LIMIT 1";
        $result = mysql_query($sql) or die("Problem obtaining record. ".mysql_error());
        return $this->word_id = mysql_result($result,0);
    }

    
}
?>