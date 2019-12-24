<?php
/**
 * Admin Panel for Codeigniter 
 * Author: Krait Solutions
 *
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Dashboard extends CI_Controller {

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
    public function __construct() 
	{
        parent::__construct();
        $this->load->library('form_validation');
        if ($this->session->userdata('is_admin_login') || $this->session->userdata('is_user_login')) {
            
        }else{
			redirect('home');
		}
		$this->load->model('patients');
    }

    public function index() { 
		$arr['data'] = $this->patients->getAllPatient();
        $this->load->view('vwDashboard',$arr);
    }
	public function patientAjax() {
		$receiptno = $this->patients->getPatientMaxId();
		$arr['receiptno'] = $receiptno;
		$arr['id'] = $this->input->post('id');
		if($this->input->post('id')>0){
			$query = $this->setting->getBrokertypebyid($this->input->post('id'));
			$arr['unit_name'] = isset($query['st_name'])?$query['st_name']:'';
		}
		if($this->input->post('saveButton')){
			$id = $this->input->post('id');
			$recieptno = $this->input->post('recieptno');
			$data['recieptno'] = $recieptno;
			$data['name'] = $this->input->post('fname').' '.$this->input->post('mname').' '.$this->input->post('sname');		
			$data['age'] = $this->input->post('age');		
			$data['address'] = $this->input->post('address');		
			$data['incharge_dr'] = $this->input->post('inchargedr');
			if($this->input->post('mlc')=='MLC'){
				$data['mlc']='MLC';
			}else{
				$data['mlc']='NonMLC';
			}			
			$data['diagnosis'] = $this->input->post('diagnosis');		
			$data['relative_name'] = json_encode($this->input->post('rname'));		
			$data['relative_phone'] = json_encode($this->input->post('rphone'));		
			$data['admissiondate'] = $this->input->post('admissiondate');		
			$data['admissiontime'] = $this->input->post('admissiontime');		
			$data['dischargedate'] = $this->input->post('dischargedate');		
			$data['dischargetime'] = $this->input->post('dischargetime');		
			$data['deathdate'] = $this->input->post('deathdate');		
			$data['deathtime'] = $this->input->post('deathtime');		
			$data['doodate'] = $this->input->post('doodate');		
			$data['dootime'] = $this->input->post('dootime');		
			$this->session->set_flashdata('btypemsg', '<div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Type Added Successfully!!!</div>');
			$this->patients->insertdata($data);
			redirect('patient/current_patient');
		}else{
			$this->load->view('PatientAjax',$arr);
		}
	}
	public function change_password()
	{
		if($this->input->post('submit'))
		{
			if($this->session->userdata('is_admin_login'))
			{
				$salt = '5&JDDlwz%Rwh!t2Yg-Igae@QxPzFTSId';
				$uid=$this->input->post('u_id');
				$old=$this->input->post('old');
				$new=$this->input->post('new');
				$confirm=$this->input->post('new_confirm');
				$enc_pass  = md5($salt.$confirm);
				if($new==$confirm)
				{
					$data =array(
					'password'=>$enc_pass,
					);
					$uid=$this->input->post('u_id');
			//	print_r($enc_pass);
			//	exit();
					$this->load->database();
					$this->db->query("UPDATE tbl_admin_users SET password='$enc_pass' WHERE id=$uid;");
					//$this->db->where('id',$this->input->post('u_id'));
					//$this->db->update('tbl_admin_users',$data);	
					//$this->load->view('vwChangePassword');
					redirect('home');
				}
				else
				{
					$arr['msg']='Password is miss match';
					$this->load->view('vwChangePassword',$arr);
				}
			}
			else if($this->session->userdata('is_user_login'))
			{
				//$salt = '5&JDDlwz%Rwh!t2Yg-Igae@QxPzFTSId';
				$uid=$this->input->post('u_id');
				$old=$this->input->post('old');
				$new=$this->input->post('new');
				$confirm=$this->input->post('new_confirm');
				$enc_pass  = md5($confirm);
				if($new==$confirm)
				{
					$data =array(
					'u_pwd'=>$enc_pass,
					);
					$this->load->database();
					$this->db->where('u_id',$this->input->post('u_id'));
					$this->db->update('tbl_user',$data);	
					//$this->load->view('vwChangePassword');
					$this->session->sess_destroy();
					redirect('home');
				}
				else
				{
					$arr['msg']='Password is miss match';
					$this->load->view('vwChangePassword',$arr);
				}
			}
			else
			{
			}
		}
		else
		{
			$this->load->view('vwChangePassword');
		}
	}
	
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */