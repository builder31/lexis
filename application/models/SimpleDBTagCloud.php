<?

//begin CloudClass
class SimpleDBTagCloud extends Model{
    private $dbname;
    private $dbhost="localhost";
    private $dbusername;
    private $dbpassword;
    public $tagTable='tags';
    private $conn;
    private $cloudSize = 30;
    
    public function SimpleDBTagCloud() {
        parent::Model();
    }
   
    public function setDbName($name) { $this->dbname=$name; }
    public function setDbHost($name) { $this->dbhost=$name; }
    public function setDbUser($name) { $this->dbusername=$name; }
    public function setDbPassword($pass) { $this->dbpassword=$pass; }
    public function setTagTable($name) { $this->tagTabl=$name; }
    public function setCloudSize($val) { $this->cloudSize=$val; }
   
    public function connect() {
        $this->conn=mysql_pconnect($this->dbhost,$this->dbusername,$this->dbpassword) or Die('Problem connecting to DB Host on first step'.mysql_error());
        @mysql_selectdb($this->dbname) or Die(mysql_error());
    }
    public function disconnect() { mysql_close($this->conn); }
    
    public function get_tag_data($source) {
        $arr=array();
        $sql="SELECT tag_keyword,occurrence FROM ".$this->tagTabl." WHERE tag_item = '".$source."' order by occurrence DESC LIMIT ".$this->cloudSize;
        $query=mysql_query($sql) or Die(mysql_error());
        while($row=mysql_fetch_assoc($query)) {
            $arr[$row['tag_keyword']] = $row['occurrence'];
        }
        return $arr;
    }
    public function get_tag_cloud($source) {
        //Default font sizes
        $min_font_size=12;
        $max_font_size=30;
        //pull in tag data
        $tags=$this->get_tag_data($source);
        $minimum_count=min(array_values($tags));
        $maximum_count=max(array_values($tags));
        $spread=$maximum_count - $minimum_count;
        
        if($spread == 0) { $spread = 1; }
        $cloud_html='';
        $cloud_tags = array(); // create an array to hold tag code
        foreach ($tags as $tag => $count) {
	$size = $min_font_size + ($count - $minimum_count) * ($max_font_size - $min_font_size) / $spread;
	$cloud_tags[] = '<a style="font-size: '. floor($size) . 'px' 
		. '" class="tag_cloud" href="http://www.google.com/search?q=' . $tag 
		. '" title="\'' . $tag  . '\' returned a count of ' . $count . '">' 
		. htmlspecialchars(stripslashes($tag)) . '</a>';
        }
        $cloud_html = join("\n", $cloud_tags) . "\n";
        return $cloud_html;
    }
    public function get_tagCloud_css() {
        print "<style type=\"text/css\">";
        print ".tag_cloud { padding: 3px; text-decoration: none; }";
        print ".tag_cloud:link  { color: #81d601; }";
        print ".tag_cloud:visited { color: #019c05; }";
        print ".tag_cloud:hover { color: #ffffff; background: #69da03; }";
        print ".tag_cloud:active { color: #ffffff; background: #ACFC65; }";
        print "</style>";
    }

}

?>
