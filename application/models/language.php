<?php
class Language extends CI_Model {
    #Configurable Table names
    private $object_table = "dict_languages";  #table containing language list
    #Dynamic private variables for object
    private $priIdxKey = "lang_id";
    private $lang_id;       //int(5) auto_increment index primary key
    private $language;      //varchar(100)
    public $active;         //int(1)
    
    function __construct() {
        parent::__construct();
    }

    //accessor methods
    public function getObjectTableName()        { return $this->object_table; }
    public function getLanguageId()             { return $this->lang_id; }
    public function getLanguage($id = null) {
        if($id == null) {   return $this->language; }
        else {
         $query = $this->db->where($this->object_table,array($this->priIdxKey => $id),1);
         return $query->result()->$this->object_table;
        }
    }
    //mutator methods
    public function setLanguage($z)             { $this->language = $z; }
    
    //db methods
    public function count_all() { return $this->db->count_all($this->object_table); }
    public function get_paged_list($limit=10,$offset=0) {
        $this->db->order_by($this->priIdxKey,'asc');
        return $this->db->get($this->object_table,$limit,$offset);
    }
    public function getRandom() {
        $sql = "SELECT ".$this->priIdxKey." FROM ".$this->object_table." ORDER BY RAND() LIMIT 1";
        $result = mysql_query($sql) or die("Problem obtaining record. ".mysql_error());
         return $this->word_id = mysql_result($result,0);
    }

    //crud methods
    public function save($data = null) {
        if(isset($data)) { $this->db->insert($this->object_table,$data); }
        else { $this->db->insert($this->object_table,$this); }
        return $this->db->insert_id();
    }
    public function update($id,$z = null) { #function explicitly mentions column name
        $this->db->where($this->priIdxKey,$id);
        if($z == null) { $this->db->update($this->object_table,$this); }
        else { $this->db->update($this->object_table,$z); }
    }
    public function load($id = null) {
        if($id == null) { $this->db->where($this->priIdxKey,$this->lang_id); }
        else { $this->db->where($this->priIdxKey,$id); }
        $query = $this->db->get($this->object_table);
        foreach ($query->result() as $row) {
            $this->lang_id = $row->lang_id;
            $this->language = $row->language;
            }
        }
    public function delete($id) {
        $this->db->where($this->priIdxKey, $id);
        $this->db->delete($this->object_table);
    }
    
}
?>