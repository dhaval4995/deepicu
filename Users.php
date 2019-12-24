<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Users extends CI_Controller {
/**
 * Admin Panel for Codeigniter 
 * Author: Krait Solutions
 *
 */
    public function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
         if ($this->session->userdata('is_admin_login') || $this->session->userdata('is_user_login')) {
            
        }else{
			redirect('home');
		}
		$this->load->helper('form');
		$this->load->helper('url');
		$this->load->model('user');
    }

    public function index() {
		$arr['user'] = $this->user->getalluser();
		$this->load->view('vwManageUser',$arr);		
    }
	
	public function add_data() {
	
		if($this->input->post('save'))
		{  
			$u_fname=mysqli_real_escape_string(get_mysqli(),$this->input->post('u_fname'));
			$u_lname=mysqli_real_escape_string(get_mysqli(),$this->input->post('u_lname'));
			$u_uname=create_username($u_fname,$u_lname);
			$u_email=$this->input->post('u_email');
			$u_mobile=$this->input->post('u_mobile');
			$u_role=$this->input->post('u_role');
			$u_regdate=date('Y-m-d');
			$u_pwd=md5('12345');
			$moduleaccess=json_encode($this->input->post('moduleaccess'));
			
			$data = array(
				'u_fname'=>$u_fname,
				'u_lname'=>$u_lname,
				'u_uname'=>$u_uname,
				'u_pwd'=>$u_pwd,
				'u_email'=>$u_email,
				'u_mobile'=>$u_mobile,
				'u_role'=>$u_role,
				'u_regdate'=>$u_regdate,
				'u_status'=>1,
				'is_deleted'=>0,
				'useraccess'=> $moduleaccess,
				
			);
			
			if($this->input->post('cid')>0){
				$this->user->updatedata($data);
				$this->session->set_flashdata('msg', '<div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>User Updated Successfully!!!</div>');
			}
			else
			{
				$this->user->insertdata($data);
			}
			redirect('users');
		}
		else{
		$this->load->view('vwAddUser');
		}
		
	}
	
	public function edit_data($cid) {
		//$cid=$this->uri->segment(3);
        $arr['active'] = 'user';
		$arr['page'] = 'user';
        $arr['cid'] = $cid;
		$parr = $this->user->editdata($cid);
		$arr['fname'] = $parr['u_fname'];
		$arr['lname'] = $parr['u_lname'];
		$arr['email'] = $parr['u_email'];
		$arr['mobile'] = $parr['u_mobile'];
		$arr['useraccess1'] = $parr['useraccess'];
		$arr['moduleaccess']=json_decode($arr['useraccess1']);
		
		$this->load->view('vwAddUser',$arr);
    }
	
	public function delete_data($cid)
	{
		$data['is_deleted']=1;
		$this->user->deletedata($data,$cid);
		$this->session->set_flashdata('msg', '<div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>User Deleted Successfully!!!</div>');
		redirect('users');
	}
	
	public function unapprove_data($id)
	{
		$this->user->unapprovedata($id);
		$this->session->set_flashdata('msg', '<div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>User Unapprove Successfully!!!</div>');
		redirect('users');
	}
	
	public function approve_data($id)
	{
		$this->user->approvedata($id);
		$this->session->set_flashdata('msg', '<div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>User Approve Successfully!!!</div>');
		redirect('users');
	}
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */

?>