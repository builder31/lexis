<?php
class Oneword_Word extends CI_Model {
    private $object_table = "oneword_words";    //table containing records
    private $word_id;           //int(5) auto_increment index primary key
    private $user_id;           //int(5) foreign key index
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
    public function getTableName()  { return $this->object_table; }
    public function getWordId()     { return $this->word_id; }
    public function getUserId()     { return $this->user_id; }
    public function getWord()       { return $this->word; }
    public function getMeaning()    { return $this->meaning; }
    public function getLangId()     { return $this->lang_id; }
    public function getPosted()     { return $this->posted; }

    //mutator methods
    public function setWordId($z)   { $this->word_id = $z;    }
    public function setUserId($z)   { $this->user_id = $z; }
    public function setWord($z)     { $this->word = $z; }
    public function setMeaning($z)  { $this->meaning = $z; }
    private function setLangId($z)  { $this->lang_id = $z; }
    public function setPosted($z)   { $this->posted = $z; }

    //db methods
    public function count_all() { return $this->db->count_all($this->object_table); }
    public function getRandom() {
        $sql = "SELECT word_id FROM ".$this->object_table." ORDER BY RAND() LIMIT 1";
        $result = mysql_query($sql) or die("Problem obtaining image ".mysql_error());
         return $this->word_id = mysql_result($result,0);
    }
    //admin control methods
    public function get_paged_list($limit=10,$offset=0) {
        $this->db->order_by('word_id','asc');
        return $this->db->get($this->object_table,$limit,$offset);
    }
    public function get_by_id($wordid) {
        $this->where('word_id',$wordid);
        return $this->db->get($this->object_table);
    }
   
    //crud methods
    public function save($data = null) {
        if(isset($data)) { $this->db->insert($this->object_table,$data); }
        else { $this->db->insert($this->object_table,$this); }
        return $this->db->insert_id();
    }
    public function update($id,$z = null)     { #function explicitly mentiones column name
        $this->db->where('word_id',$id);
        if($z == null) { $this->db->update($this->object_table,$this); }
        else { $this->db->update($this->object_table, $z); }
        }
    public function load($id = null)    { #function explicitly mentions column name
        if($id == null) { $this->db->where('word_id',$this->word_id); }
        else { $this->db->where('word_id',$id); }
        $query = $this->db->get($this->object_table);
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
    public function delete($id) {
        $this->db->where('word_id', $id);   #function explicitly mentions column name
        $this->db->delete($this->object_table);
    }
}
?>
