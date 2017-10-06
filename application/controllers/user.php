<?
class User extends CI_Controller {
    
    public $appLinks = array (
	"MyInfo" => "/user/myInfo"
    );
    public $facebook;
    private $is_logged_in;
    
    function __construct() {
        parent::__construct();
        $this->load->helper('security','form');
        $this->facebook = new Facebook(array(
                'appId' => $this->config->item('facebook_app_id'),
                'secret' => $this->config->item('facebook_secret_key'),
                'cookie' => true,
                ));
        $this->load->model('App_User','app_user');
    }

    function index() {
        /*
         User login should be processed using either the internal userId or the fb_uid property.
         Both should be evaluated and use whichever exists
        */
        $this->load->view('base/header');   
        #process facebookl login information
        $fb_session = $this->facebook->getSession();
        $fb_me = null;
        $fb_uid = null;
        // Session based API call.
        if($fb_session) {
            try {
                $fb_uid = $this->facebook->getUser();
                $fb_me = $this->facebook->api('/me?fields=id,name,link,email');
            } catch (FacebookApiException $e) { error_log($e);  }
            #this only applies to facebook users
            $this->session->set_userdata('fb_me', $me);
            $this->session->set_userdata('fb_uid', $uid);
            #evaluate fb data populated into the session variables
            if($this->session->userdata['fb_me']) {
                if(!array_key_exists('app_user_id',$this->session->userdata)) {
                    #check to see if user email is registered
                    if($this->app_user->findByEmail($me['email'])) {  
                        $this->session->set_userdata('app_user_id',$this->app_user->findByEmail($me['email']));
                    }
                }            
        } else { /*redirect to facebook registration form */}
        }
        #evaluate session for non facebook users
        if(!array_key_exists('app_user_id',$this->session->userdata)) {
            $this->load->view('user/user_login_form');
        } else { $this->load->view('welcome_message'); }
                        
        $this->load->view('base/sidebar');
        $this->load->view('base/footer');	
    }
    
    function test() {
        #print_r($this->session->userdata);
        #$this->db->where('email',$this->session->userdata['app_user_id']);
        #$this->db->where('password',md5('P@ssw0rd'));
        #$query = $this->db->get('app_users');
        #$row = $query->row();
        #print_r($row);
        #echo "<br />".md5('P@ssw0rd')."<br />";
        $this->load->view('example');
        
    }
    
    function logout() {
        $this->session->sess_destroy();
        redirect('/user/');
    }
    //facebook functions
        
    function fblogin() {
        if ($_REQUEST) {
         $response = $this->parse_signed_request($_REQUEST['signed_request'], 
                                   '356411ed9468d8a511aadc8afa56bd9b');
         #echo '<pre>';
         #print_r($response);
         #echo '</pre>';
        } else {
          echo '$_REQUEST is empty';
        }
        //move REQUEST vars to vals array
        $vals['user_id']  = $session->userdata['fb_uid'];
        $vals['email']    = $response['registration']['email'];
        #$vals['password'] = $response['registration']['password'];
        $vals['password'] = "f@cEb0()K!";
        $vals['location'] = $response['registration']['location'];
        $vals['language'] = $response['registration']['language'];
        if($query = $this->app_user->save($vals)) {
                $data['content'] = 'signup successful';
		$this->session->set_userdata('app_user_id', $query);
                redirect('/user/');
            }
    }

    function parse_signed_request($signed_request, $secret) {
        list($encoded_sig, $payload) = explode('.', $signed_request, 2); 
        // decode the data
        $sig = $this->base64_url_decode($encoded_sig);
        $data = json_decode($this->base64_url_decode($payload), true);
    
        if (strtoupper($data['algorithm']) !== 'HMAC-SHA256') {
        error_log('Unknown algorithm. Expected HMAC-SHA256');
        return null;
        }
    
        // check sig
        $expected_sig = hash_hmac('sha256', $payload, $secret, $raw = true);
        if ($sig !== $expected_sig) {
        error_log('Bad Signed JSON signature!');
        return null;
        }
    
        return $data;
    }
    
    function base64_url_decode($input) {
        return base64_decode(strtr($input, '-_', '+/'));
    }
}
?>
