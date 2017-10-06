<?php
class Tribe_Name extends CI_Model{
    private $tribe_id;       //int(5)
    private $tribe_name;      //varchar(100)
    public $active;         //int(1)
    
    function __construct() {
        parent::__construct();
    }

    //accessor methods
    public function getTribeId() { $this->tribe_id; }
    public function getTribeName() { $this->tribe_name; }
    //mutator methods
    public function setTribeId($z) { $this->tribe_id = $z; }
    public function setTribeName($z) { $this->tribe_name = $z; }
    
    //object methods
    public function getRandom() {
        $sql = "SELECT tribe_id FROM tribe_names ORDER BY RAND() LIMIT 1";
        $result = mysql_query($sql) or die("Problem obtaining image ".mysql_error());
        $this->media_id = mysql_result($result,0);
    }

    //crud methods
    public function save()      {   $this->db->insert('tribe_names',$this);          }
    public function update($id) {   $this->db->update('tribe_names', $this, $id);    }
    public function load($id) {
        $this->db->where('tribe_id',$id);
        $query = $this->db->get('tribe_names');
        foreach ($query->result() as $row) {
            $this->tribe_id = $row->tribe_id;
            $this->tribe_names = $row->tribe_names;
        }
    }
    public function delete($id) {
        $this->db->where('tribe_id', $id);
        $this->db->delete('tribe_names');
    }
    
}
?>