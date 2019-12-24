<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Settings extends CI_Controller {
/**
 * Admin Panel for Codeigniter 
 * Author: Krait Solutions
 *
 */
	var $Rolearr = array();
    public function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
        if ($this->session->userdata('is_admin_login') || $this->session->userdata('is_user_login')) {
            
        }else{
			redirect('home');
		}
		$this->load->model('setting');
		$this->load->model('customer');
		$this->Rolearr = $this->setting->getUsersRole('all');
    }

    public function index() {
		if (!file_exists('./uploads/company')) {
			mkdir('./uploads/company', 0777, true);
		}
        $arr['page'] = 'setting';
        $arr['active'] = '';
        $arr['usersrole'] = $this->Rolearr;
        $this->load->view('vwSetting',$arr);
    }
	
	public function companyupdate() {
        $arr['page'] = 'companydetail';
		$arr['active'] = 'system';
		$arr['usersrole'] = $this->Rolearr;
        if(isset($_POST['companyupdate'])){
            $this->form_validation->set_rules('companyname', 'Company Name', 'required');
			//$this->form_validation->set_rules('companyemail', 'Email', 'required');
			
			$config['upload_path']          = './uploads/company/';
			$config['allowed_types']        = '*';
			$config['max_size']             = 1000;
			$config['max_width']            = 1024;
			$config['max_height']           = 768;

			$this->load->library('upload', $config);
			
            if($this->form_validation->run() == FALSE) {
				$err['error'] = validation_errors();
                $this->load->view('vwSettingBusiness',$err);
            }else  if ( ! $this->upload->do_upload('companylogo') && !empty($_FILES['companylogo']['name']))
			{
				$err = array('error' => $this->upload->display_errors());

				$this->load->view('vwSettingBusiness', $err);
			}else{
				$data = array(
						'companyname' => $this->input->post('companyname'),
						'companytelephone1' => $this->input->post('companytelephone1'),
						'companytelephone2' => $this->input->post('companytelephone2'),
						'companyemail' => $this->input->post('companyemail'),
						'companyfax' => $this->input->post('companyfax'),
						'companytinno' => $this->input->post('companytinno'),
						'companycstno' => $this->input->post('companycstno'),
						'companygstno' => $this->input->post('companygstno'),
						'companyeccno' => $this->input->post('companyeccno'),
						'companyvatno' => $this->input->post('companyvatno'),
						'companycsttinno' => $this->input->post('companycsttinno'),
						'companypanno' => $this->input->post('companypanno'),
						'companyrange' => $this->input->post('companyrange'),
						'companydivision' => $this->input->post('companydivision'),
						'companycommissionrate' => $this->input->post('companycommissionrate'),
						'companyslogan' => $this->input->post('companyslogan'),
						'companywebsite' => $this->input->post('companywebsite'),
						'companyaddress' => $this->input->post('companyaddress'),
						'companycity' => $this->input->post('companycity'),
						'companystate' => $this->input->post('companystate'),
						'companycountry' => $this->input->post('companycountry'),
						'companypostalcode' => $this->input->post('companypostalcode'),
						'companyaddress1' => $this->input->post('companyaddress1'),
						'companycity1' => $this->input->post('companycity1'),
						'companystate1' => $this->input->post('companystate1'),
						'companycountry1' => $this->input->post('companycountry1'),
						'companypostalcode1' => $this->input->post('companypostalcode1'),
						'bankacno' => $this->input->post('acno'),
						'bankacname' => $this->input->post('acname'),
						'bankname' => $this->input->post('bankname'),
						'bankifsc' => $this->input->post('ifsc'),
						'bankbranch' => $this->input->post('branch'),
						'bankactype' => $this->input->post('actype'),
						);
						
				if (!empty($_FILES['companylogo']['name']))
				{
					$logodata = $this->upload->data();	
					$data['companylogo'] = $logodata['file_name'];
					$existinglogo = getconfigMeta('companylogo');
					unlink('./uploads/company/'.$existinglogo);
				}
				$this->setting->updateSetting($data);
				$this->session->set_flashdata('cmpmsg', '<div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Company Data Updated Successfully!!!</div>');
				//$this->load->view('vwSetting',$arr);	
				redirect('settings/companyupdate');
			}
		}else{
			$arr['state']=$this->customer->getstate();
			$this->load->view('vwSettingBusiness',$arr);
		}
    }
	
	public function userrole() {
        $arr['page'] = 'role';
        $arr['active'] = 'user';
        $arr['usersrole'] = $this->Rolearr;
        $this->load->view('vwSettingUserRole',$arr);
    }
    public function smtpupdate() {
        $arr['page'] = 'smtpdetail';
		$arr['active'] = 'other';
		$arr['usersrole'] = $this->Rolearr;
        if(isset($_POST['smtpupdate'])){
            $this->form_validation->set_rules('smtpserver1', 'Server address', 'required');
            $this->form_validation->set_rules('smtpserverport1', 'Server port', 'required');
            $this->form_validation->set_rules('smtpusername1', 'Username', 'required');
            $this->form_validation->set_rules('smtppassword1', 'Password', 'required');

            if($this->form_validation->run() == FALSE) {
				$arr['error'] = validation_errors();
                $this->load->view('vwSettingSmtp',$arr);
            }else{
				$data = array(
						'smtpserver1' => $this->input->post('smtpserver1'),
						'smtpserverport1' => $this->input->post('smtpserverport1'),
						'smtpusername1' => $this->input->post('smtpusername1'),
						'smtppassword1' => $this->input->post('smtppassword1'),
						'smtpserver2' => $this->input->post('smtpserver2'),
						'smtpserverport2' => $this->input->post('smtpserverport2'),
						'smtpusername2' => $this->input->post('smtpusername2'),
						'smtppassword2' => $this->input->post('smtppassword2'),
						'smtpserver3' => $this->input->post('smtpserver3'),
						'smtpserverport3' => $this->input->post('smtpserverport3'),
						'smtpusername3' => $this->input->post('smtpusername3'),
						'smtppassword3' => $this->input->post('smtppassword3'),
						);
				$this->setting->updateSetting($data);
				$this->session->set_flashdata('msg', '<div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Data Updated Successfully!!!</div>');
				redirect('settings/smtpupdate');
			}
		}else{
			$this->load->view('vwSettingSmtp',$arr);
		}
    }
	public function smsupdate() {
        $arr['page'] = 'smsdetail';
		$arr['active'] = 'other';
		$arr['usersrole'] = $this->Rolearr;
        if(isset($_POST['smsupdate'])){
            $this->form_validation->set_rules('smsapi1', 'API', 'required');
            $this->form_validation->set_rules('smsusername1', 'Username', 'required');
            $this->form_validation->set_rules('smspassword1', 'Password', 'required');
            $this->form_validation->set_rules('smssenderid1', 'Sender ID', 'required');

            if($this->form_validation->run() == FALSE) {
				$err['error'] = validation_errors();
                $this->load->view('vwSettingSms',$err);
            }else{
				$data = array(
						'smsapi1' => $this->input->post('smsapi1'),
						'smsusername1' => $this->input->post('smsusername1'),
						'smspassword1' => $this->input->post('smspassword1'),
						'smssenderid1' => $this->input->post('smssenderid1'),
						'smsapi2' => $this->input->post('smsapi2'),
						'smsusername2' => $this->input->post('smsusername2'),
						'smspassword2' => $this->input->post('smspassword2'),
						'smssenderid2' => $this->input->post('smssenderid2'),
						);
				$this->setting->updateSetting($data);
				$this->session->set_flashdata('msg', '<div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Data Updated Successfully!!!</div>');
				//$this->load->view('vwSetting',$arr);	
				redirect('settings/smsupdate');
			}
		}else{
			$this->load->view('vwSettingSms',$arr);
		}
    }
	
	
	//Add New User Type
	public function add_usertype() {
		$arr['active'] = 'user';
		$arr['page'] = 'role';
		$arr['id'] = 0;
		if($this->input->post('addrole')){
			$this->form_validation->set_rules('userrole', 'User Role', 'required');
            $this->form_validation->set_rules('userrolealias', 'User Role Alias', 'required');
			 if($this->form_validation->run() == FALSE) {
				$arr['error'] = validation_errors();
                $this->load->view('vwAddUserType',$arr);
            }else{
				$userrole = $this->input->post('userrole');
				$alias = $this->input->post('userrolealias');
				$roletype = $this->input->post('roletype');
				$data['role_name'] = $userrole;
				$data['role_alias'] = $alias;
				$data['role_type'] = $roletype;
				if($this->input->post('id')){
					$this->setting->updaterole($data);
					$this->session->set_flashdata('rolemsg', '<div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>User Role Updated Successfully!!!</div>');
				}else{
					$this->setting->addrole($data);
					$this->session->set_flashdata('rolemsg', '<div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>User Role Added Successfully!!!</div>');
				}
				redirect('settings/userrole');
			}
		}else{
			$this->load->view('vwAddUserType',$arr);
		}
	}
	//End Add New User Type
	
	//Edit User Type
	public function edit_usertype($id) {
		$arr['page'] = 'role';
		$arr['id'] = $id;
		$query = $this->setting->getusertypebyid($id);
		$arr['userrole'] = $query['role_name'];
		$arr['userrolealias'] = $query['role_alias'];
		$arr['roletype'] = $query['role_type'];
        $this->load->view('vwAddUserType',$arr);
	}
	//End Edit User Type
	//Delete User Type
	public function delete_usertype($id) {
        // Code goes here
		$query = $this->setting->deleteusertype($id);
		$this->session->set_flashdata('rolemsg', '<div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>User Role Deleted Successfully!!!</div>');
		redirect('settings/userrole');
    }
	//End Delete User Type
	
	//Tax
	public function taxupdate() {
        $arr['page'] = 'taxdetail';
		$arr['active'] = 'other';
		$arr['productTax'] = $this->setting->getTax('product');
		$arr['shippingTax'] = $this->setting->getTax('shipping');
		$this->load->view('vwSettingTax',$arr);
	}
	public function taxAjax() {
		$arr['type'] = $this->input->post('type');
		$arr['taxid'] = $this->input->post('taxid');
		$query = $this->setting->getTaxbyid($this->input->post('taxid'));
		$arr['taxname'] = isset($query['tax_name'])?$query['tax_name']:'';
		$arr['taxvalue'] = isset($query['tax_value'])?$query['tax_value']:'';
		$arr['status'] = isset($query['tax_status'])?$query['tax_status']:'';
		if($this->input->post('ptype')){
			$taxid = $this->input->post('taxid');
			$ptype = $this->input->post('ptype');
			$taxname = $this->input->post('taxname');
			$taxvalue = $this->input->post('taxvalue');
			$status = $this->input->post('status');
			$data['tax_name'] = $taxname;
			$data['tax_value'] = $taxvalue;
			$data['tax_type'] = $ptype;
			$data['tax_status'] = $status;
			
			if($taxid>0){
				//update
				$this->session->set_flashdata('taxmsg', '<div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Tax Updated Successfully!!!</div>');
				$this->setting->updatetax($data);
				redirect('settings/taxupdate');
			}else{
				//add	
				$this->session->set_flashdata('taxmsg', '<div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Tax Added Successfully!!!</div>');
				$this->setting->addtax($data);
				redirect('settings/taxupdate');
			}
		}else{
			$this->load->view('taxAjax',$arr);
		}
	}
	
	//End Tax
	
	//Currency 
	public function currency() {
        $arr['page'] = 'currency';
		$arr['active'] = 'other';
		$arr['currency'] = $this->setting->getCurrency();
		$this->load->view('vwSettingCurrency',$arr);
	}
	public function currencyAjax() {
		$arr['id'] = $this->input->post('id');
		$query = $this->setting->getCurrencybyid($this->input->post('id'));
		$arr['currency_name'] = isset($query['currency_name'])?$query['currency_name']:'';
		$arr['currency_code'] = isset($query['currency_code'])?$query['currency_code']:'';
		$arr['currency_symbol'] = isset($query['currency_symbol'])?$query['currency_symbol']:'';
		$arr['currency_status'] = isset($query['currency_status'])?$query['currency_status']:'';
		if($this->input->post('saveButton')){
			$id = $this->input->post('id');
			$ptype = $this->input->post('ptype');
			$currency_name = $this->input->post('currency_name');
			$currency_code = $this->input->post('currency_code');
			$currency_symbol = $this->input->post('currency_symbol');
			$currency_status = $this->input->post('currency_status');
			$data['currency_name'] = $currency_name;
			$data['currency_code'] = $currency_code;
			$data['currency_symbol'] = $currency_symbol;
			$data['currency_status'] = $currency_status;
			
			if($id>0){
				//update
				$this->session->set_flashdata('taxmsg', '<div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Currency Updated Successfully!!!</div>');
				$this->setting->updatecurrency($data);
				redirect('settings/currency');
			}else{
				//add	
				$this->session->set_flashdata('taxmsg', '<div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Currency Added Successfully!!!</div>');
				$this->setting->addcurrency($data);
				redirect('settings/currency');
			}
		}else{
			$this->load->view('currencyAjax',$arr);
		}
	}
	//
	
	//Item Unit 
	public function itemunit() {
        $arr['page'] = 'itemunit';
		$arr['active'] = 'other';
		$arr['units'] = $this->setting->getItemunit();
		$this->load->view('vwSettingItemunit',$arr);
	}
	public function itemunitAjax() {
		$arr['id'] = $this->input->post('id');
		if($this->input->post('id')>0){
			$query = $this->setting->getItemunitbyid($this->input->post('id'));
			$arr['unit_name'] = isset($query['unit_name'])?$query['unit_name']:'';
			$arr['unit_status'] = isset($query['unit_status'])?$query['unit_status']:'';
		}
		if($this->input->post('saveButton')){
			$id = $this->input->post('id');
			$unit_name = $this->input->post('unit_name');
			$unit_status = $this->input->post('unit_status');
			$data['unit_name'] = $unit_name;
			$data['unit_code'] = strtolower($unit_name);
			$data['unit_status'] = $unit_status;
			
			if($id>0){
				//update
				$this->session->set_flashdata('taxmsg', '<div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Unit Updated Successfully!!!</div>');
				$this->setting->updateitemunit($data);
				redirect('settings/itemunit');
			}else{
				//add	
				$this->session->set_flashdata('taxmsg', '<div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Unit Added Successfully!!!</div>');
				$this->setting->additemunit($data);
				redirect('settings/itemunit');
			}
		}else{
			$this->load->view('unitAjax',$arr);
		}
	}
	//
	//Suppier Type 
	public function suppliertype() {
        $arr['page'] = 'suppliertype';
		$arr['active'] = 'other';
		$arr['units'] = $this->setting->getSuppliertype();
		$this->load->view('vwSettingSuppliertype',$arr);
	}
	public function suppliertypeAjax() {
		$arr['id'] = $this->input->post('id');
		if($this->input->post('id')>0){
			$query = $this->setting->getSuppliertypebyid($this->input->post('id'));
			$arr['unit_name'] = isset($query['st_name'])?$query['st_name']:'';
		}
		if($this->input->post('saveButton')){
			$id = $this->input->post('id');
			$unit_name = $this->input->post('unit_name');
			$data['st_name'] = $unit_name;
			$data['st_code'] = preg_replace('/\s+/', '', strtolower($unit_name));
			
			if($id>0){
				//update
				$this->session->set_flashdata('taxmsg', '<div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Type Updated Successfully!!!</div>');
				$this->setting->updateSuppliertype($data);
				redirect('settings/suppliertype');
			}else{
				//add	
				$this->session->set_flashdata('taxmsg', '<div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Type Added Successfully!!!</div>');
				$this->setting->addSuppliertype($data);
				redirect('settings/suppliertype');
			}
		}else{
			$this->load->view('SuptypeAjax',$arr);
		}
	}
	//
	
	//SMS List
	public function sms_list() {
        $arr['page'] = 'sms';
		$arr['active'] = 'other';
		$arr['smsdata'] = $this->setting->getSMSList();
		$this->load->view('vwSettingSMSList',$arr);
	}
	public function add_sms($id=0) {
        $arr['page'] = 'sms';
		$arr['active'] = 'other';
		$arr['id'] = $id;
		if($id>0){
			$query = $this->setting->getSMSData($id);
			$arr['smsreason'] = $query['sms_reason'];
			$arr['smstext'] = $query['sms_text'];
		}
		if(isset($_POST['submit'])){
			$this->form_validation->set_rules('smsreason', 'SMS Reason', 'required');			
			$this->form_validation->set_rules('smstext', 'SMS Text', 'required');			
			
			$this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');
		
			if ($this->form_validation->run() == FALSE) // validation hasn't been passed
			{
				$err['error'] = validation_errors();	
				$err['id'] = 0;
				$this->load->view('vwAddSMS',$err);
			}
			else // passed validation proceed to post success logic
			{
				$form_data = array(
					'sms_reason' => @$this->input->post('smsreason'),
					'sms_text' => @$this->input->post('smstext'),
				);
						
				// run insert model to write data to db
			
				if($this->input->post('id')){
					$this->setting->updateSMS($form_data);
					$this->session->set_flashdata('smsmsg', '<div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>SMS Data Updated Successfully!!!</div>');
				}else{
					$this->setting->insertSMS($form_data);		
					$this->session->set_flashdata('smsmsg', '<div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>SMS Data Added Successfully!!!</div>');
				}
				redirect('settings/sms_list');
			}
		}else{
			$this->load->view('vwAddSMS',$arr);		
		}
	}
	public function delete_sms($id) {
        // Code goes here
		$arr['page'] = 'sms';
		$arr['active'] = 'other';
		$query = $this->setting->deletesms($id);
		$this->session->set_flashdata('smsmsg', '<div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>SMS Data Deleted Successfully!!!</div>');
		redirect('settings/sms_list');
    }	

	//Email List
	public function email_list() {
        $arr['page'] = 'email';
		$arr['active'] = 'other';
		$arr['emaildata'] = $this->setting->getEmailList();
		$this->load->view('vwSettingEmailList',$arr);
	}
	public function add_email($id=0) {
        $arr['page'] = 'email';
		$arr['active'] = 'other';
		$arr['id'] = $id;
		if($id>0){
			$query = $this->setting->getEmailData($id);
			$arr['emailsubject'] = $query['email_subject'];
			$arr['emailcontent'] = $query['email_content'];
			$arr['emailreason'] = $query['email_reason'];
		}
		if(isset($_POST['submit'])){
			$this->form_validation->set_rules('emailsubject', 'Email Subject', 'required');			
			$this->form_validation->set_rules('emailcontent', 'Email Content', 'required');			
			$this->form_validation->set_rules('emailreason', 'Email Reason', 'required');			
			
			$this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');
		
			if ($this->form_validation->run() == FALSE) // validation hasn't been passed
			{
				$err['error'] = validation_errors();	
				$err['id'] = 0;
				$this->load->view('vwAddEmail',$err);
			}
			else // passed validation proceed to post success logic
			{
				$form_data = array(
					'email_subject' => @$this->input->post('emailsubject'),
					'email_content' => @$this->input->post('emailcontent'),
					'email_reason' => @$this->input->post('emailreason'),
				);
						
				// run insert model to write data to db
			
				if($this->input->post('id')){
					$this->setting->updateEmail($form_data);
					$this->session->set_flashdata('emailmsg', '<div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>SMS Data Updated Successfully!!!</div>');
				}else{
					$this->setting->insertEmail($form_data);		
					$this->session->set_flashdata('emailmsg', '<div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>SMS Data Added Successfully!!!</div>');
				}
				redirect('settings/email_list');
			}
		}else{
			$this->load->view('vwAddEmail',$arr);		
		}
	}
	public function delete_email($id) {
        // Code goes here
		$arr['page'] = 'email';
		$arr['active'] = 'other';
		$query = $this->setting->deleteemail($id);
		$this->session->set_flashdata('emailmsg', '<div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>SMS Data Deleted Successfully!!!</div>');
		redirect('settings/email_list');
    }
	public function emailsetting() {
        $arr['page'] = 'emailsetting';
		$arr['active'] = 'other';
		if(isset($_POST['submit'])){
			$udata = $this->input->post();
			unset($udata['submit']);
			$this->setting->updateSetting($udata);
			$this->session->set_flashdata('cmpmsg', '<div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Data Updated Successfully!!!</div>');
			redirect('settings/emailsetting');
		}else{
			$this->load->view('vwemailsetting',$arr);
		}
    }	
	//SO Status 
	public function sostatus() {
        $arr['page'] = 'sostatus';
		$arr['active'] = 'other';
		$arr['units'] = $this->setting->getSOStatus();
		$this->load->view('vwSettingSOStatus',$arr);
	}
	public function sostatusAjax() {
		$arr['id'] = $this->input->post('id');
		if($this->input->post('id')>0){
			$query = $this->setting->getSOStatusbyid($this->input->post('id'));
			$arr['unit_name'] = isset($query['st_name'])?$query['st_name']:'';
		}
		if($this->input->post('saveButton')){
			$id = $this->input->post('id');
			$unit_name = $this->input->post('unit_name');
			$data['st_name'] = $unit_name;
			$data['st_code'] = preg_replace('/\s+/', '', strtolower($unit_name));
			
			if($id>0){
				//update
				$this->session->set_flashdata('taxmsg', '<div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Status Updated Successfully!!!</div>');
				$this->setting->updateSOStatus($data);
				redirect('settings/sostatus');
			}else{
				//add	
				$this->session->set_flashdata('taxmsg', '<div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Status Added Successfully!!!</div>');
				$this->setting->addSOStatus($data);
				redirect('settings/sostatus');
			}
		}else{
			$this->load->view('StatussoAjax',$arr);
		}
	}
	//PO Status 
	public function postatus() {
        $arr['page'] = 'postatus';
		$arr['active'] = 'other';
		$arr['units'] = $this->setting->getPOStatus();
		$this->load->view('vwSettingPOStatus',$arr);
	}
	public function postatusAjax() {
		$arr['id'] = $this->input->post('id');
		if($this->input->post('id')>0){
			$query = $this->setting->getPOStatusbyid($this->input->post('id'));
			$arr['unit_name'] = isset($query['st_name'])?$query['st_name']:'';
		}
		if($this->input->post('saveButton')){
			$id = $this->input->post('id');
			$unit_name = $this->input->post('unit_name');
			$data['st_name'] = $unit_name;
			$data['st_code'] = preg_replace('/\s+/', '', strtolower($unit_name));
			
			if($id>0){
				//update
				$this->session->set_flashdata('taxmsg', '<div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Status Updated Successfully!!!</div>');
				$this->setting->updatePOStatus($data);
				redirect('settings/postatus');
			}else{
				//add	
				$this->session->set_flashdata('taxmsg', '<div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Status Added Successfully!!!</div>');
				$this->setting->addPOStatus($data);
				redirect('settings/postatus');
			}
		}else{
			$this->load->view('StatusAjax',$arr);
		}
	}
	//
	//PO Template List
	public function po_template() {
		$arr['tempdata'] = $this->setting->getPOTempList();
		$this->load->view('vwSettingPOTemplateList',$arr);
	}
	public function add_potemp($id=0) {
		$arr['id'] = $id;
		if($id>0){
			$query = $this->setting->getPOTempData($id);
			$arr['tempname'] = $query['potemp_name'];
			$arr['tempcontent'] = $query['potemp_content'];
			$arr['temptype'] = $query['potemp_type'];
			$arr['tempterms'] = $query['potemp_terms'];
		}
		$type = $this->setting->getSuppliertype();
		$arr['type'] = $type;
		if(isset($_POST['submit'])){
			$this->form_validation->set_rules('tempname', 'Template Name', 'required');			
			$this->form_validation->set_rules('temptype', 'Template Type', 'required');			
			$this->form_validation->set_rules('tempcontent', 'Template Content', 'required');			
			
			$this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');
		
			if ($this->form_validation->run() == FALSE) // validation hasn't been passed
			{
				$err['error'] = validation_errors();	
				$err['id'] = 0;
				$this->load->view('vwAddPOTemplate',$err);
			}
			else // passed validation proceed to post success logic
			{
				$form_data = array(
					'potemp_name' => @$this->input->post('tempname'),
					'potemp_type' => @$this->input->post('temptype'),
					'potemp_content' => @$this->input->post('tempcontent'),
					'potemp_terms' => @$this->input->post('tempterms'),
				);
						
				// run insert model to write data to db
			
				if($this->input->post('id')){
					$this->setting->updatPOTemp($form_data);
					$this->session->set_flashdata('tempmsg', '<div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>PO Template Updated Successfully!!!</div>');
				}else{
					$this->setting->insertPOTemp($form_data);		
					$this->session->set_flashdata('tempmsg', '<div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>PO Template Added Successfully!!!</div>');
				}
				redirect('settings/po_template');
			}
		}else{
			$this->load->view('vwAddPOTemplate',$arr);
		}
	}
	public function delete_potemp($id) {
        // Code goes here
		$query = $this->setting->deletepotemp($id);
		$this->session->set_flashdata('tempmsg', '<div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>PO Template Deleted Successfully!!!</div>');
		redirect('settings/po_template');
    }
	//Stock Unit 
	public function stockunit() {
        $arr['page'] = 'stockunit';
		$arr['active'] = 'other';
		$arr['units'] = $this->setting->getStockunit();
		$this->load->view('vwSettingStockunit',$arr);
	}
	public function stockunitAjax() {
		$arr['id'] = $this->input->post('id');
		if($this->input->post('id')>0){
			$query = $this->setting->getStockunitbyid($this->input->post('id'));
			$arr['unit_name'] = isset($query['unit_name'])?$query['unit_name']:'';
		}
		if($this->input->post('saveButton')){
			$id = $this->input->post('id');
			$unit_name = $this->input->post('unit_name');
			$unit_status = $this->input->post('unit_status');
			$data['unit_name'] = $unit_name;
			$data['unit_code'] = strtolower($unit_name);
			
			if($id>0){
				//update
				$this->session->set_flashdata('taxmsg', '<div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Unit Updated Successfully!!!</div>');
				$this->setting->updatestockunit($data);
				redirect('settings/stockunit');
			}else{
				//add	
				$this->session->set_flashdata('taxmsg', '<div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Unit Added Successfully!!!</div>');
				$this->setting->addstockunit($data);
				redirect('settings/stockunit');
			}
		}else{
			$this->load->view('stockunitAjax',$arr);
		}
	}
	//
	//SO Template List
	public function so_template() {
		$arr['tempdata'] = $this->setting->getSOTempList();
		$this->load->view('vwSettingSOTemplateList',$arr);
	}
	public function add_sotemp($id=0) {
		$arr['id'] = $id;
		if($id>0){
			$query = $this->setting->getSOTempData($id);
			$arr['tempname'] = $query['potemp_name'];
			$arr['tempcontent'] = $query['potemp_content'];
			$arr['temptype'] = $query['potemp_type'];
			$arr['tempterms'] = $query['potemp_terms'];
		}
		if(isset($_POST['submit'])){
			$this->form_validation->set_rules('tempname', 'Template Name', 'required');			
			$this->form_validation->set_rules('tempcontent', 'Template Content', 'required');			
			
			$this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');
		
			if ($this->form_validation->run() == FALSE) // validation hasn't been passed
			{
				$err['error'] = validation_errors();	
				$err['id'] = 0;
				$this->load->view('vwAddSOTemplate',$err);
			}
			else // passed validation proceed to post success logic
			{
				$form_data = array(
					'potemp_name' => @$this->input->post('tempname'),
					'potemp_content' => @$this->input->post('tempcontent'),
					'potemp_terms' => @$this->input->post('tempterms'),
				);
						
				// run insert model to write data to db
			
				if($this->input->post('id')){
					$this->setting->updatSOTemp($form_data);
					$this->session->set_flashdata('tempmsg', '<div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>SO Template Updated Successfully!!!</div>');
				}else{
					$this->setting->insertSOTemp($form_data);		
					$this->session->set_flashdata('tempmsg', '<div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>SO Template Added Successfully!!!</div>');
				}
				redirect('settings/so_template');
			}
		}else{
			$this->load->view('vwAddSOTemplate',$arr);
		}
	}
	public function delete_sotemp($id) {
        // Code goes here
		$query = $this->setting->deletesotemp($id);
		$this->session->set_flashdata('tempmsg', '<div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>SO Template Deleted Successfully!!!</div>');
		redirect('settings/so_template');
    }
	public function default_sotemp($id) {
        // Code goes here
		$query = $this->setting->defaultsotemp($id);
		$this->session->set_flashdata('tempmsg', '<div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>SO Template Set Successfully!!!</div>');
		redirect('settings/so_template');
    }
	//SO Document
	public function sodoc() {
        $arr['page'] = 'sodoc';
		$arr['active'] = 'other';
		$arr['docs'] = $this->setting->getSODoc();
		$this->load->view('vwSettingSODoc',$arr);
	}
	public function sodocAjax() {
		$arr['id'] = $this->input->post('id');
		if($this->input->post('id')>0){
			$query = $this->setting->getSODocbyid($this->input->post('id'));
			$arr['doc_name'] = isset($query['docname'])?$query['docname']:'';
		}
		if($this->input->post('saveButton')){
			$id = $this->input->post('id');
			$doc_name = $this->input->post('doc_name');
			$data['docname'] = $doc_name;
			$data['docalias'] = preg_replace('/\s+/', '', strtolower($doc_name));
			
			if($id>0){
				//update
				$this->session->set_flashdata('taxmsg', '<div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Document Updated Successfully!!!</div>');
				$this->setting->updateSODoc($data);
				redirect('settings/sodoc');
			}else{
				//add	
				$this->session->set_flashdata('taxmsg', '<div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Document Added Successfully!!!</div>');
				$this->setting->addSODoc($data);
				redirect('settings/sodoc');
			}
		}else{
			$this->load->view('DocsoAjax',$arr);
		}
	}
	 public function msglist() {
		$arr['data'] = $this->setting->getallmsg();
		$this->load->view('vwMessageList',$arr);
			
    }
	public function add_msg() {
	
		if($this->input->post('save'))
		{  
			$title=$this->input->post('title');
			$msg=$this->input->post('msg');
			$data = array(
				'title'=>$title,				
				'msg'=>$msg,				
				'is_deleted'=>'0',
			);			
			if($this->input->post('cid')>0){				
				$this->setting->updatedata($data);
				$this->session->set_flashdata('msg', '<div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Customer Updated Successfully!!!</div>');
			}
			else
			{
				$this->setting->insertdata($data);
			}
			redirect('settings/msglist');
		}
		else{
			$this->load->view('vwAddMessage');
		}		
	}
	public function edit_data($cid) {
		//$cid=$this->uri->segment(3);
        $arr['active'] = 'user';
		$arr['page'] = 'user';
        $arr['cid'] = $cid;
		$parr = $this->setting->editdata($cid);
		$arr['title'] = $parr['title'];
		$arr['msg'] = $parr['msg'];
        $this->load->view('vwAddMessage',$arr);
    }
	
	public function delete_data($cid)
	{
		$data['is_deleted']='1';
		$this->setting->deletedata($data,$cid);		
		$this->session->set_flashdata('msg', '<div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Customer Deleted Successfully!!!</div>');
		redirect('settings/msglist');
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */