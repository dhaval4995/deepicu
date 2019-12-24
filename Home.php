<?php
/**
 * Admin Panel for Codeigniter 
 * Author: Krait Solutions
 *
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Home extends CI_Controller {

    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     * 		http://example.com/index.php/welcome
     * 	- or -  
     * 		http://example.com/index.php/welcome/index
     * 	- or -
     * Since this controller is set as the default controller in 
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see http://codeigniter.com/user_guide/general/urls.html
     */
    public function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
		$this->load->library('session');
    }

    public function index() {
        //if ($this->session->userdata('is_admin_login')) {
          //  redirect('dashboard',true);
        //} else {
			$this->load->view('vwLogin');
        //}
		
    }

     public function do_login() {

        if ($this->session->userdata('is_admin_login')) {
            redirect('patient/current_patient');
        } else {
            $user = $this->input->post('username');
            $password = $this->input->post('password');
            $type = $this->input->post('type');
			
			
				$this->form_validation->set_rules('username', 'Username', 'required');
				$this->form_validation->set_rules('password', 'Password', 'required');

				if ($this->form_validation->run() == FALSE) {
					$err['error'] = validation_errors();
					$this->load->view('vwLogin',$err);
				} else {
				
					
						$salt = '5&JDDlwz%Rwh!t2Yg-Igae@QxPzFTSId';
						$enc_pass  = md5($salt.$password);
					//print_r($enc_pass);
					//exit();
						$sql = "SELECT * FROM tbl_admin_users WHERE username = ? AND password = ? AND user_status = ?";
						$val = $this->db->query($sql,array($user ,$enc_pass ,'A'));

						if ($val->num_rows()) {
							foreach ($val->result_array() as $recs => $res) {
								$isadmin = false;
								if($res['user_type']=="superadmin" || $res['user_type']=="admin"){
									$isadmin = true;
								}
								$this->session->set_userdata(array(
									'id' => $res['id'],
									'username' => $res['username'],
									'displayname' => $res['first_name'].' '.$res['last_name'],
									'email' => $res['email'],                            
									'is_admin_login' => $isadmin,
									'user_type' => $res['user_type']
										)
								);
							}							
							redirect('patient/current_patient');
						} else {
							$err['error'] = '<strong>Access Denied</strong> Invalid Username/Password<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
							$this->load->view('vwLogin', $err);
						}
					
					
				}
			}
			
        }

	public function password_recovery(){
		$this->load->model('user');
		if($this->input->post('submit')){
			$this->form_validation->set_rules('email', 'Email Address', 'required');
			if($this->form_validation->run() == FALSE) {
				$err['error'] = validation_errors();
                $this->load->view('vwPasswordrecovery',$err);
            }else{
				$email = $this->input->post('email');
				$sql = "SELECT * FROM tbl_admin_users WHERE email = ?";
                $val = $this->db->query($sql,array($email));
                if ($val->num_rows) {
					foreach ($val->result_array() as $recs => $res) {
						$err['useremail'] = $res['email'];
						$err['username'] = $res['first_name'].' '.$res['last_name'];
						$err['userID'] = $res['id'];
						$err['mobile'] = $res['mobile'];
						$err['userEmailValid'] = true;
						$this->load->view('vwPasswordrecovery',$err);
					}
				}else{
					$err['error'] = 'Email address not exists in Our System.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';	
					$this->load->view('vwPasswordrecovery',$err);
				}
			}
		}else if($this->input->post('getmethod')){
			$accountemail = $this->input->post('accountemail');
			$accountmobile = $this->input->post('accountmobile');
			$codemethod = $this->input->post('codemethod');
			if($codemethod == "email"){
				$this->load->library('email');
				// Sender email address
				$this->email->from(getconfigMeta('smtpusername1'), getconfigMeta('cmpname'));
				// Receiver email address
				$this->email->to($accountemail);
				// Subject of email
				$subject = "[DO NOT REPLY] New password of ".getconfigMeta('cmpname');
				$this->email->subject($subject);
				// Message in email
				$code = rand(100000,999999);
				$link = base_url()."home/password_change/email=".$accountemail."/code=".$code;
				$message = "Dear User

					You are receiving this notification because you have (or someone pretending to be you has) requested a new
					password be sent for your account on ".getconfigMeta('cmpname').".

					If you did not request this notification then please ignore it, if you keep receiving it please contact the site
					administrator.

					To use the new password you need to activate it. To do this click the link provided below.

					".$link."
					This is system Generated Email do not reply to this email. If you have received this email mistakenly or any
					query related to ".getconfigMeta('cmpname')." service/product please contact to ".getconfigMeta('cmpemail')."

					Sincerely
					".getconfigMeta('cmpname')." MANAGEMENT";
				$this->email->message($message);
				if ($this->email->send()) {
					$err['message'] = 'Email Successfully Send !<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
					$date = date("Y-m-d");
					$data['useremail'] = $accountemail;
					$data['activation_code'] = $code;
					$data['date'] = $date;
					$data['attemp'] = 0;
					$this->user->insertForgotPass($data);	
				} else {
					$err['error'] =  $this->email->print_debugger();
				}
				$this->load->view('vwPasswordrecovery',$err);
			}else if($codemethod == "mobile"){
				$mobileno = $accountmobile;
				$email = $accountemail;
				$date = date("Y-m-d");
				$code = rand(100000,999999);
				$message = "Dear User, Your ".getconfigMeta('cmpname')." verification code is ".$code;
				if(sendSMS($mobileno,$message)){
					$data['useremail'] = $email;
					$data['activation_code'] = $code;
					$data['date'] = $date;
					$data['attemp'] = 0;
					$this->user->insertForgotPass($data);
					$err['message'] = 'Message Successfully Send !<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
					$err['smssend'] = true;
				}else{
					$err['error'] = 'Message Not Send !<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
				}
				$this->load->view('vwPasswordrecovery',$err);
			}
		}else if($this->input->post('checksmscode')){
			$mobilecode = $this->input->post('mobilecode');
			$sql = "SELECT * FROM forgot_password WHERE activation_code = ?";
			$val = $this->db->query($sql,array($mobilecode));
			if ($val->num_rows) {
				redirect('home/smscode/'.$mobilecode);
			}else{
				$err['smssend'] = true;
				$err['error'] = 'Invalid Code.Please try again.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';	
				$this->load->view('vwPasswordrecovery',$err);
			}
		}else if($this->input->post('newpwd')){
			$password = $this->input->post('password');
			$password1 = $this->input->post('password1');
			$email = $this->input->post('email');
			$code = $this->input->post('code');
			$this->form_validation->set_rules('password', 'New Password', 'required');
			$this->form_validation->set_rules('password1', 'Confirm Password', 'required');
			$err['verify'] = true;
			$err['email'] = $email;
			$err['code'] = $code;
			if($this->form_validation->run() == FALSE) {
				$err['error'] = validation_errors();
                $this->load->view('vwPasswordrecovery',$err);
            }else{
				if($password!=$password1){
					$err['error'] = "Password does not match.";
					$this->load->view('vwPasswordrecovery',$err);
				}else{
					$salt = '5&JDDlwz%Rwh!t2Yg-Igae@QxPzFTSId';
					$enc_pass  = md5($salt.$password);
					$data['password'] = $enc_pass;
					$this->db->where('email',$email);
					$this->db->update('tbl_admin_users',$data);
					$this->db->where('activation_code', $code);
					$this->db->delete('forgot_password');
					redirect('home', 'refresh');
				}				
			}
		}else{
			$this->load->view('vwPasswordrecovery');
		}
	}	
	
    public function smscode($code){
		$sql = "SELECT * FROM forgot_password WHERE activation_code = ?";
		$val = $this->db->query($sql,array($code));
		if ($val->num_rows) {
			$err['verify'] = true;
			foreach ($val->result_array() as $recs => $res) {
				$err['email'] = $res['useremail'];
				$err['code'] = $res['activation_code'];
				$newattemp = $res['attemp'] + 1;
				$this->db->where('activation_code',$code);
				$data['attemp'] = $newattemp;
				$this->db->update('forgot_password',$data);
			}
			$this->load->view('vwPasswordrecovery',$err);
		}else{
			$err['smssend'] = true;	
			$err['error'] = 'Invalid Code.Please try again.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';	
			$this->load->view('vwPasswordrecovery',$err);
		}
	}
	
    public function logout() {
        $this->session->unset_userdata('id');
        $this->session->unset_userdata('username');
        $this->session->unset_userdata('email');
        $this->session->unset_userdata('user_type');
        $this->session->unset_userdata('is_admin_login');   
        $this->session->sess_destroy();
        $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0");
        $this->output->set_header("Pragma: no-cache");
        redirect('home');
    }
	public function backup(){
		$this->load->dbutil();   
		$backup =& $this->dbutil->backup();  
		$this->load->helper('file');
		write_file('<?php echo base_url();?>/downloads', $backup);
		$this->load->helper('download');
		force_download('Billing.sql', $backup);
	}
	
	public function robot_password()
	{
		if($this->input->post('submit'))
		{
			$robotpwd=$this->customer->checkrobotpsw();
			$salt = '5&JDDlwz%Rwh!t2Yg-Igae@QxPzFTSId';
			$pwd=$this->input->post('pwd');
			$enc_pass  = md5($salt.$pwd);
			if($robotpwd==$enc_pass)
			{
				$this->db->truncate('tbl_account');
				$this->db->truncate('tbl_bank');
				$this->db->truncate('tbl_customer');
				$this->db->truncate('tbl_employee');
				$this->db->truncate('tbl_extra_stock');
				$this->db->truncate('tbl_extra_stock_detail');
				//$this->db->truncate('tbl_item');
				$this->db->truncate('tbl_opening_amt');
				$this->db->truncate('tbl_opening_balance_bank');
				$this->db->truncate('tbl_opening_balance_customer');
				$this->db->truncate('tbl_opening_balance_stock');
				$this->db->truncate('tbl_payment');
				$this->db->truncate('tbl_purchase');
				$this->db->truncate('tbl_purchase_item');
				$this->db->truncate('tbl_purchase_order');
				$this->db->truncate('tbl_purchase_order_item');
				$this->db->truncate('tbl_purchase_return');
				$this->db->truncate('tbl_purchase_return_item');
				$this->db->truncate('tbl_sale');
				$this->db->truncate('tbl_sale_item');
				$this->db->truncate('tbl_sale_return');
				$this->db->truncate('tbl_sale_return_item');
				//$this->db->truncate('tbl_tax');
				$this->db->truncate('tbl_unit');
				$this->db->truncate('tbl_user');
				
				$this->load->view('vwRobotPassword');
			}
			else
			{
				$arr['msg']='Password is miss match';
				$this->load->view('vwRobotPassword',$arr);
			}
		}
		else
		{
		$this->load->view('vwRobotPassword');
		}
	}

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */