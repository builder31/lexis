<?php
class User extends CI_Model{
    private $object_table = "app_users";    //table containing user objects
    private $user_id;        //int(5)
    private $email;             //varchar(255)
    private $name;              //varchar(255)
    private $password;          //varchar(16)
    private $dob;               //datetime
    private $city;         //varchar(100)
    private $state;         //varchar(100)
    public $submissions;        //int(5)
    public $score;              //decimal(5,0)
    public $active;             //int(1)
    private $priv;              //int(1)

    function __construct() {
        parent::__construct();
    }

    //accessor methods
    public function getUserId() { return $this->user_id; }
    public function getEmail() { return $this->email; }
    public function getName() { return $this->name; }
    private function getPassword() { return $this->password; }
    public function getDob() { return $this->dob; }
    public function getCity() { return $this->city; }
    public function getState() { return $this->state; }
    public function getPriv() { return $this->priv; }

    //mutator methods
    public function setUserId($z) { $this->user_id = $z; }
    public function setEmail($z) { $this->email = $z; }
    public function setName($z) { $this->name = $z; }
    private function setPassword($z) { $this->password = $z; }
    public function setDob($z) { $this->dob = $z; }
    public function setCity($z) { $this->city; }
    public function setState($z) { $this->city; }
    public function setPriv($z) { $this->priv = $z; }

    //db methods
    public function count_all() { return $this->db->count_all($this->object_table); }
    public function get_paged_list($limit = 10, $offset = 0) { 
        $this->db->order_by('user_id','asc');   #function explicitly mentions column name
        return $this->db->get($this->object_table,$limit,$offset);
    }
    public function getRandom() {
        $this->db->order_by("user_id","desc");
        $this->db->limit(1);
        $this->db->get($this->object_table);
        foreach ($query->result() as $row) { $this->user_id = $row->user_id; }
    }
    
    //object function
    public function calculateAge() {
        #to be coded later
    }
    function validate($emailAddr) {
        if($username != null) {
            $this->db->where('email',$emailAddr);
            $q=$this->db->get($this->object_table);
            
            if($q->num_rows == 1) { return true; }
            else { return false; }
        } else { return "There was a null value submitted."; }
    }
/*Description of Privilege levels
0 - viewer [this is the default and can only view data]
1 - moderator [this is an elevated viewer which can view data, deactive users and submissions within own org, create new users within own org, can also email users directly within own org. data changes to org can be requested ]
2 - staff [ this is elevated moderator and for internal staff only which can deactivate any user, submission, email any user, create new users and organizations. this user can be overridden by superadmin]
3 - student [ this is an elevated viewer which can view data, submit new items within own org only, see own stats, email moderator for own org, change and deactivate own submissions only]
        */
    public function changePrivileges($lvl) {
        if((!isset($lvl) || ($lvl <0) || ($lvl > 3))) {   $lvl = 0; }
        $this->priv = $lvl;
    }
    //crud methods
    public function save($data = null) {
        if(isset($data)) { $this->db->insert($this->object_table,$data); }
        else { $this->db->insert($this->object_table,$this); }
        return $this->db->insert_id();
    }    
    public function update($id,$z = null)     { #function explicitly mentiones column name
        $this->db->where('user_id',$id);
        if($z == null) { $this->db->update($this->object_table,$this); }
        else { $this->db->update($this->object_table, $z); }
        }

    public function load($id) {
        if($id == null) { $this->db->where('user_id',$this->word_id); }
        else { $this->db->where('user_id',$id); }
        $query = $this->db->get($this->object_table);
        foreach ($query->result() as $row) {
            $this->user_id = $row->user_id;
            $this->email = $row->email;
            $this->name = $row->name;
            $this->dob = $row->dob;
            $this->city = $row->city;
            $this->state = $row->state;
            $this->submissions = $row->submissions;
            $this->score = $row->score;
            $this->priv = $row->priv;
        }
    }
    public function delete($id) {
        $this->db->where('user_id', $id);   #function explicitly mentions column name
        $this->db->delete($this->object_table);
    }
    
}
?>