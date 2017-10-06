<?php
class Word extends CI_Model{
    private $word_id;           //int(5)
    private $user_id;           //int(5)
    private $word;              //varchar(100)
    private $meaning;           //text
    private $lang_id;           //int(5)
    private $posted;            //datetime
    public $active;             //int(1)
    public $foundHelpful;       //int(5)

    function __construct() {
        parent::__construct();
    }
    //accessor methods
    public function getWordId()     { return $this->word_id; }
    public function getUserId()     { return $this->user_id; }
    public function getWord()       { return $this->word; }
    public function getMeaning()    { return $this->meaning; }
    private function getLangId()    { return $this->lang_id; }
    public function getPosted()     { return $this->posted; }
    //mutator methods
    public function setWordId($z)   { $this->word_id = $z;    }
    public function setUserId($z)   { $this->user_id = $z; }
    public function setWord($z)     { $this->word = $z; }
    public function setMeaning($z)  { $this->meaning = $z; }
    private function setLangId($z)  { $this->lang_id = $z; }
    public function setPosted($z)   { $this->posted = $z; }
    //crud methods
    public function save($data)     {
        if(isset($data)) {   $this->db->insert('words',$data);             }
        else {  $this->db->insert('words',$this);   }
    }
    public function update($id)     { $this->db->update('words', $this, $id);    }
    public function load($id)       {
        $this->db->where('word_id',$id);
        $query = $this->db->get('words');
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
        $this->db->where('word_id', $id);
        $this->db->delete('words');
    }
    //object methods
    public function getRandom() {
        $sql = "SELECT word_id FROM words ORDER BY RAND() LIMIT 1";
        $result = mysql_query($sql) or die("Problem obtaining image ".mysql_error());
        $this->media_id = mysql_result($result,0);
    }
    public function calculateAge() {
        
    }
    
}
?>