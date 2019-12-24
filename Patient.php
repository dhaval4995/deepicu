<?php
/**
 * Admin Panel for Codeigniter 
 * Author: Krait Solutions
 *
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Patient extends CI_Controller {

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
		$arr['data'] = $this->patients->getAllPatientDepositeList();
		// echo "<pre>";
		// print_r($arr);
		// exit();
		$this->load->view('vwManagePatient',$arr);
	   /* echo "<pre>";
	   print_r($arr);
	   exit(0);	 */
    }

    public function add_tablet()
    {
    	$advised_name=$this->input->post('advised_name');
    	$data=array('advised_name'=>$advised_name);
    	$this->patients->add_tablet($data);
    	$this->index();
    }
	public function upload_doc() {
		$arr['data'] = $this->patients->getAllPatient();
		$this->load->view('vwUploadDoc',$arr);
		// print_r($arr);
		// exit;	
    }
	public function search_list() {
		$start = $this->input->post('start');
		$end = $this->input->post('end');
		$arr['start']=$start;
		$arr['end']=$end;
		$start = date('Y-m-d',strtotime($start));
		$end = date('Y-m-d',strtotime($end));
		$arr['data'] = $this->patients->SearchPatientDepositeList($start,$end);
		$this->load->view('vwManagePatient',$arr);
		// print_r($arr);
		// exit;	
	}

	public function search_bill(){
		$start = $this->input->post('start');
		$end = $this->input->post('end');
		$arr['start']=$start;
		$arr['end']=$end;	
		$start = date('Y-m-d',strtotime($start));
		$end = date('Y-m-d',strtotime($end));
		$arr['data'] = $this->patients->SearchPatientBillList($start,$end);
		$arr['depositelist'] = $this->patients->SearchPatientBillList1();
		// echo "<pre>";	
		// print_r($arr);
		// exit();
		$this->load->view('vwManageBillList',$arr);
	}


	public function search_discharge() {
		$valueToSearch = $this->input->post('valueToSearch');
		$arr['valueToSearch']=$valueToSearch;
		$arr['data'] = $this->patients->SearchDischargePatient($valueToSearch);
		$this->load->view('vwSearchDischarge',$arr);
		// print_r($arr);
		// exit;	
	}
	

	public function current_patient() {
		$arr['data'] = $this->patients->getPatients();
		$this->load->view('vwCurrentPatient',$arr);
		// print_r($arr);
		// exit;	
    }
	public function discharge_list() {
		$arr['data'] = $this->patients->getAllDischargePatient();
		$this->load->view('vwDischargeList',$arr);
		// print_r($arr);
		// exit;			
	}

	public function DeleteCurrentpatient($patient_id){
		// print_r($patient_id);
		// exit();
        $this->patients->DeleteCurrentpatient($patient_id); 
        $this->patients->DeleteCurrentpatient1($patient_id);  
        	$this->session->set_flashdata('msg', '<div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Patient Deleted Successfully!!!</div>');
        	redirect('patient/current_patient/');
	}

	public function DeleteDischargepatient($patient_id){
		
		$arr=$this->patients->findpatientrecieptno($patient_id);
		$recieptno=$arr['recieptno'];
		
		$this->patients->DeleteDischargepatient($patient_id);
		$this->patients->DeleteDischargepatient1($patient_id);
		$this->patients->DeleteDischargepatient2($patient_id);
		$this->patients->DeleteDischargepatient3($recieptno);
		$this->session->set_flashdata('msg', '<div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Patient Deleted Successfully!!!</div>');
        	redirect('patient/dischargepatient/');

	}
	public function add_data() {
		$receiptno = $this->patients->getPatientMaxId();
		$arr['receiptno'] = $receiptno;
		// print_r($arr);
		// exit;
		if($this->input->post('save'))
		{  
			$datearr = array();
			// $amtarr = array();
			for($i=0;$i<count($this->input->post('amount'));$i++){
				if($this->input->post('amount')[$i]>0){
					$datearr[] = date('Y-m-d',strtotime($this->input->post('date')[$i]));
					$amtarr[] = $this->input->post('amount')[$i];
					// $printarr[] = $this->input->post('print')[$i];
					$amtdata['ptn_id'] = $this->input->post('receiptno');
					$amtdata['payment_mode'] = $this->input->post('payment');
					$amtdata['chequeno'] = $this->input->post('chequeno');
					$amtdata['bankname'] = $this->input->post('bankname');
					$amtdata['amount'] = $this->input->post('amount')[$i];
					// $amtdata['print'] = $this->input->post('print')[$i];
					$amtdata['date'] = date('Y-m-d',strtotime($this->input->post('date')[$i]));

					if($this->input->post('depositid')[$i]>0){
						$this->patients->updatedepositedata($amtdata,$this->input->post('depositid')[$i]);
					}else{
						$this->patients->insertdepositedata($amtdata);
						// print_r($amtdata);
						// exit;	
					}
					
				}				
			}
			$data = array(
				'recieptno'=>$this->input->post('receiptno'),				
				//'case_type'=>$this->input->post('type'),				
				'name'=>$this->input->post('name'),				
				'mobile'=>$this->input->post('mobile'),				
				'city'=>$this->input->post('city'),				
				'district'=>$this->input->post('district'),				
				'age'=>$this->input->post('age'),				
				'payment_mode'=>$this->input->post('payment'),				
				'chequeno'=>$this->input->post('chequeno'),				
				'bankname'=>$this->input->post('bankname'),				
				'date'=>json_encode($datearr),				
				'amount'=>json_encode($amtarr),
				// 'print'=>$this->input->post('print'),
			);			
			$cid=$this->input->post('cid');
			if($this->input->post('cid')>0){				
				$this->patients->updatedata($data,$cid);
				$this->session->set_flashdata('msg', '<div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Patient Updated Successfully!!!</div>');
			}
			else
			{
				$insert_id = $this->patients->insertdata($data);
			}
			redirect('patient/edit_data/'.$this->input->post('cid'));
		}
		else{
			$this->load->view('vwAddPatient',$arr);
		}		
	}
	public function update_depositdata() {
		if($this->input->post('save'))
		{  
			$datearr = array();
				$amtdata['amount'] =$this->input->post('amount');
				// print_r($amtdata);
				// exit();
				// $amtdata['print'] = $this->input->post('print');
				$amtdata['date'] = date('Y-m-d',strtotime($this->input->post('date')));
				$id=$this->input->post('id');

				$this->patients->updatedepositedata($amtdata,$id);
			$cid=$this->input->post('cid');	//reciept no of tbl patient		
			$data = array(
				'recieptno'=>$this->input->post('receiptno'),				
				//'case_type'=>$this->input->post('type'),				
				'name'=>$this->input->post('name'),				
				'mobile'=>$this->input->post('mobile'),				
				'city'=>$this->input->post('city'),				
				'district'=>$this->input->post('district'),				
				'age'=>$this->input->post('age'),
				'amount'=>$this->input->post('amount'),
				'address'=>$this->input->post('address'),			
				'payment_mode'=>$this->input->post('payment'),				
				'chequeno'=>$this->input->post('chequeno'),				
				'bankname'=>$this->input->post('bankname'),
			);	
			// print_r($cid);
			// exit();		
				$this->patients->updatedata($data,$cid);
				$this->session->set_flashdata('msg', '<div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Patient Updated Successfully!!!</div>');
			
			redirect('patient/edit_depositdata/'.$this->input->post('id'));
		}
				
	}
	public function discharge() {
		$patientid=$this->input->get('var1');
		$srno=$this->input->get('var2');
		$arr['srno']=$srno;
		$arr['patientid'] = $patientid;
		$arr['advised']=$this->patients->getAdvised();
		$parr = $this->patients->getPatientdischargeno($patientid);
		$arr['discharge_id'] = $parr['discharge_id'];
		$arr['cid'] = $parr['discharge_id'];
		$patientdata = $this->patients->getPatientbyId($parr['patient_id']);
		$arr['ptndata'] = $patientdata;
		$arr['name'] = $parr['ptn_name'];
		$arr['age'] = $parr['ptn_age'];
		$arr['gender'] = ucfirst($patientdata['gender']);
		$arr['patient_address'] = $parr['patient_address'];
		$arr['incharge_dr'] = $parr['incharge_dr'];
		$arr['door_no'] = $parr['door_no'];
		$arr['diagnosis'] = $parr['diagnosis'];
		$arr['doa'] = $parr['doa'];
		$arr['toa'] = $parr['toa'];
		$arr['dod'] = $parr['dod'];
		$arr['tod'] = $parr['tod'];
		$arr['doo'] = $parr['doo'];
		$arr['too'] = $parr['too'];
		$arr['mlc'] = $parr['mlc']; 
		$arr['drmobile'] = $parr['dr_mobile'];
		$arr['disease_history'] = $parr['disease_history'];
		$arr['advised1'] =explode(",", $parr['advised']);
		$arr['other'] = $parr['other'];
		$arr['closedate'] = $parr['close_date'];
		$patientdata = $this->patients->getAllPatientByName();
		$arr['patientdata'] = $patientdata;
		$disdata = $this->patients->getIndoorNo();
		$arr['indoor_no'] = $disdata['discharge_id'] + 1;
		if($this->input->post('save'))
		{  
			$patientid1=$this->input->post('patientid');
			$srno1=$this->input->post('srno');
			$patnid = $this->input->post('name');
			$ptndata = $this->patients->getPatientbyId($patnid);
			$advised=implode(",",(array)$this->input->post('advised'));
			$data = array(
				'ptn_name'=>$ptndata['name'],				
				'ptn_age'=>$ptndata['age'],				
				'patient_id'=>$this->input->post('name'),				
				'patient_address'=>$this->input->post('patient_address'),				
				'incharge_dr'=>$this->input->post('inchargedr'),				
				'door_no'=>$this->input->post('doorno'),				
				'diagnosis'=>$this->input->post('diagnosis'),				
				'doa'=>$this->input->post('doa'),
				'toa'=>$this->input->post('toa'),				
				'dod'=>$this->input->post('dod'),
				'tod'=>$this->input->post('tod'),				
				'doo'=>$this->input->post('doo'),
				'too'=>$this->input->post('too'),
				'mlc'=>$this->input->post('mlc'),
				'disease_history'=>$this->input->post('disease'),				
				'advised'=>$advised,				
				'other'=>$this->input->post('other'),				
				'dr_mobile'=>$this->input->post('drmobile'),				
				'close_date'=>$this->input->post('closedate'),
				'discharge_date'=>date('Y-m-d'),
			);

			$pdata = array(			
				'incharge_dr'=>$this->input->post('inchargedr'),		
				'diagnosis'=>$this->input->post('diagnosis'),
			);
			$this->patients->updatepatientdata($pdata,$patnid);			
			if($this->input->post('cid')>0){				
				$this->patients->updatedischargedata($data);
				$this->session->set_flashdata('msg', '<div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Patient Updated Successfully!!!</div>');
			}
			else
			{
				$insert_id = $this->patients->insertdischargedata($data);
			}
			redirect(base_url().'patient/discharge/?var1='.$patientid1.'&var2='.$srno1);
		}
		else{
			$this->load->view('vwDischargeForm',$arr);
		}		
	}
	public function receipt($id){
		$data1 = $this->patients->getPatientbyId($id);
		$data['print']=$data1['print']+1;
		$this->patients->updatepatientdata($data,$id);
		$arr['data'] = $this->patients->getPatientbyId($id);
		$this->load->view('vwPrintReceipt',$arr);
	}
	public function other_receipt($id){
		$arr['ptn_id'] = $this->uri->segment(3);
		$arr['depositid'] = $this->uri->segment(4);
		$arr['date'] = $this->uri->segment(5);
		$arr['amount'] = $this->uri->segment(6);
		$arr['payment_mode'] = $this->uri->segment(7);
		$arr['chequeno'] = $this->uri->segment(8);
		$arr['bankname'] = $this->uri->segment(9);
		$arr['print'] = $this->uri->segment(10);
		$did=$arr['depositid'];
		// $data1 = $this->patients->getPatientbyId($id);
		// $data2=$this->patients->getPatientbyId2($id);
		$data1=$this->patients->getdepositebyid($did);
		
		$data=$data1['print']+1;
		// print_r($data);
		// exit();
		$this->patients->updatepatientdata1($data,$did);
		$arr['data1']=$data;
		// $this->patients->updatepatientdata($data,$id);
		$arr['data'] = $this->patients->getPatientbyId($id);
		// echo "<pre>";
		// print_r($arr);
		// exit();
		$this->load->view('vwPrintOtherReceipt',$arr);
	}
	public function dischargeform(){
		$id=$this->input->get('var1');
		$srno=$this->input->get('var2');
		$data11 = $this->patients->getDischargePatientbyId($id);
		$data1['print'] = $data11['print']+1;
		$arr['ptndata'] = $this->patients->getPatientbyId($id);
		$this->patients->updatedischargepatientdata($data1,$id);
		$data = $this->patients->getDischargePatientbyId($id);
		$arr['data'] = $data;
		$arr['srno']=$srno;
		$arr['patientdata'] = $this->patients->getPatientbyId($data['patient_id']);
		$this->load->view('vwPrintDischargeForm',$arr);
	}
	public function edit_data($cid) {
		//$cid=$this->uri->segment(3);        
        $arr['cid'] = $cid;
		$parr = $this->patients->getPatientbyId($cid);
		$arr['depositelist'] = $this->patients->getPatientDeposite($parr['recieptno']);
		// echo "<pre>";
		// print_r($arr);
		// exit();
		$arr['recieptno'] = $parr['recieptno'];
		// print_r($arr);
		// exit();
		$arr['casetype'] = $parr['case_type'];
		$arr['name'] = $parr['name'];
		$arr['address'] = $parr['address'];
		$arr['age'] = $parr['age'];
		$arr['payment'] = $parr['payment_mode'];
		$arr['chequeno'] = $parr['chequeno'];
		$arr['bankname'] = $parr['bankname'];
		$arr['amount'] =json_decode($parr['amount']);
		$arr['print'] =json_decode($parr['print']);
		$arr['date'] =json_decode($parr['date']);
        $this->load->view('vwAddPatient',$arr);
    }
	public function edit_depositdata($id) {
		//$cid=$this->uri->segment(3);        
        $darr = $this->patients->getPatientDepositeByid($id);
		$cid = $darr['ptn_id'];
		$arr['cid'] = $cid;		//reciept no
		$arr['id'] = $id;      //deposit id
			
		$parr = $this->patients->getPatientbyId2($cid);
		// print_r($parr);
		// exit();	
		$arr['depositelist'] = $this->patients->getPatientDeposite($parr['recieptno']);
		$arr['recieptno'] = $parr['recieptno'];
		$arr['casetype'] = $parr['case_type'];
		$arr['name'] = $parr['name'];
		$arr['address'] = $parr['address'];
		$arr['age'] = $parr['age'];
		$arr['payment'] = $parr['payment_mode'];
		$arr['chequeno'] = $parr['chequeno'];
		$arr['bankname'] = $parr['bankname'];
		$arr['amount'] =$darr['amount'];
		// $arr['print'] =$darr['print'];
		// echo "<pre>";
		// print_r($arr);
		// exit();
		$arr['date'] =$darr['date'];
        $this->load->view('vwEditDeposite',$arr);
    }
	public function edit_dischargedata($cid) {
		//$cid=$this->uri->segment(3); 
		$patientdata = $this->patients->getAllPatientByName();
		$arr['patientdata'] = $patientdata;		
        $arr['cid'] = $cid;
        $arr['advised']=$this->patients->getAdvised();
		$parr = $this->patients->getDischargePatientbyId($cid);
		$arr['patientid'] = $parr['patient_id'];
		$patientdata = $this->patients->getPatientbyId($parr['patient_id']);
		$arr['ptndata'] = $patientdata;
		$patientdata = $this->patients->getPatientbyId($parr['patient_id']);
		$arr['age'] = $patientdata['age'];
		$arr['gender'] = ucfirst($patientdata['gender']);
		$arr['address'] = $parr['patient_address'];
		$arr['incharge_dr'] = $parr['incharge_dr'];
		$arr['door_no'] = $parr['door_no'];
		$arr['diagnosis'] = $parr['diagnosis'];
		$arr['doa'] = $parr['doa'];
		$arr['toa'] = $parr['toa'];
		$arr['dod'] = $parr['dod'];
		$arr['tod'] = $parr['tod'];
		$arr['doo'] = $parr['doo'];
		$arr['too'] = $parr['too'];
		$arr['disease_history'] = $parr['disease_history'];
		$arr['advised1'] =explode(",", $parr['advised']);
		$arr['other'] = $parr['other'];
		$arr['closedate'] = date('d-m-Y',strtotime($parr['close_date']));
        $this->load->view('vwDischargeForm',$arr);
    }
	public function delete_data($cid)
	{
		$this->patients->deletedata($cid);		
		$this->session->set_flashdata('msg', '<div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Patient Deleted Successfully!!!</div>');
		redirect('patient');
	}
	public function delete_depositedata($cid)
	{
		$this->patients->deletedepositedata($cid);		
		$this->session->set_flashdata('msg', '<div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Deposite Deleted Successfully!!!</div>');
		redirect('patient');
	}
	public function delete_dischargedata($cid)
	{
		$this->patients->deletedischargedata($cid);		
		$this->session->set_flashdata('msg', '<div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Patient Deleted Successfully!!!</div>');
		redirect('patient/discharge_list');
	}
	public function getpatientDetail()
	{
		$id = $this->input->post('id');
		$data = $this->patients->getPatientbyId($id);		
		$dataArr['age']=$data['age'];
		$dataArr['sex']=ucfirst($data['gender']);
		$dataArr['address']=$data['address'];
		$dataArr['incharge_dr']=$data['incharge_dr'];
		$dataArr['diagnosis']=$data['diagnosis'];
		echo json_encode($dataArr);
	}
	public function billlist()
	{
		$arr['data']=$this->patients->getAllBillData();
		$this->load->view('vwPatientBillList',$arr);
	}
	public function add_charges(){
		$patient_id=$this->input->post('patient_id');
		$charges=$this->input->post('charges');
		$this->patients->add_charges(array('patient_id'=>$patient_id,'charges'=>$charges));
	    $this->bill($patientid);
	}
	// $total = 0;
	public function bill()
	{
		$patientid=$this->input->get('var1');
		$srno=$this->input->get('var2');
		$arr['patientid']=$patientid;
		$arr['ptndata'] = $this->patients->getPatientbyId($patientid);
		$billdata=$this->patients->getBillBypatientId($patientid);
		$arr['cid']=$billdata['bill_id'];
		$arr['patient_id']=$billdata['patient_id'];
		$arr['billno']=$billdata['bill_no'];
		$arr['date']=date('d-m-Y',strtotime($billdata['bill_date']));
		$arr['qty']=json_decode($billdata['qty']);
		$arr['rate']=json_decode($billdata['rate']);
		$arr['amount']=json_decode($billdata['amount']);
		$arr['othername']=json_decode($billdata['othername']);
		$arr['otherqty']=json_decode($billdata['otherqty']);
		$arr['otherrate']=json_decode($billdata['otherrate']);
		$arr['otheramount']=json_decode($billdata['otheramount']);
		$arr['discountamount']=$billdata['discountamount'];
		$arr['totalfinalamount']=$billdata['totalamount'];
		$arr['patientdata'] = $this->patients->getDischargePatient();
		$arr['patientid']=$patientid;
		$arr['srno']=$srno;
		if($this->input->post('save')){
			$patientid=$this->input->get('var1');
			$cid=$this->input->post('cid');
			// $cid=
			// print_r($cid);
			// exit();
			$totalamt = 0;
			$totalotheramt = 0;
			for($i=0;$i<count($this->input->post('qty'));$i++){
				$totalamt +=$this->input->post('amount')[$i];
			}
			for($i=0;$i<count($this->input->post('otherqty'));$i++){
				if($this->input->post('otheramount')[$i]>0){
					$totalotheramt +=$this->input->post('otheramount')[$i];					
				}
			}
			$data=array(
				'patient_id'=>$patientid,
				'bill_no'=>$this->input->post('billno'),
				'bill_date'=>date('Y-m-d',strtotime($this->input->post('billdate'))),
				'qty'=>json_encode($this->input->post('qty')),
				'rate'=>json_encode($this->input->post('rate')),
				'amount'=>json_encode($this->input->post('amount')),
				'othername'=>json_encode($this->input->post('othername')),
				'otherqty'=>json_encode($this->input->post('otherqty')),
				'otherrate'=>json_encode($this->input->post('otherrate')),
				'otheramount'=>json_encode($this->input->post('otheramount')),
				'discountamount'=>$this->input->post('discountamount'),
				'totalamount'=>($totalamt+$totalotheramt-$this->input->post('discountamount')),
			);
			if($this->input->post('cid')>0){
				$this->patients->updateBillData($data,$this->input->post('cid'));
				$this->session->set_flashdata('msg', '<div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Patient Bill Added Successfully!!!</div>');
			}else{
				$this->patients->insertBillData($data);
				$this->session->set_flashdata('msg', '<div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Patient Bill Added Successfully!!!</div>');
			}
			redirect(base_url().'patient/bill/?var1='.$patientid.'&var2='.$srno);
			//redirect('patient/bill/'.$this->input->post('name'));
		}
		$this->load->view('vwCreateBill',$arr);
	}
	public function edit_billdata($id)
	{
		$arr['patientdata'] = $this->patients->getDischargePatient();		
		$arr['ptndata'] = $this->patients->getPatientbyId($id);
		$billdata = $this->patients->getBillById($id);
		$arr['cid']=$billdata['bill_id'];
		$arr['patientid']=$billdata['patient_id'];
		$arr['billno']=$billdata['bill_no'];
		$arr['date']=date('d-m-Y',strtotime($billdata['bill_date']));
		$arr['qty']=json_decode($billdata['qty']);
		$arr['rate']=json_decode($billdata['rate']);
		$arr['amount']=json_decode($billdata['amount']);		
		$arr['othername']=json_decode($billdata['othername']);
		$arr['otherqty']=json_decode($billdata['otherqty']);
		$arr['otherrate']=json_decode($billdata['otherrate']);
		$arr['otheramount']=json_decode($billdata['otheramount']);
		$this->load->view('vwCreateBill',$arr);
	}
	public function getDischargePatientdetail()
	{
		$patientid = $this->input->post('id');
		$data = $this->patients->getPatientdischargeno($patientid);
		echo $data['discharge_id'];
	}
	public function delete_billdata($id)
	{
		$this->patients->deleteBillData($id);
		$this->session->set_flashdata('msg', '<div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Patient Bill Deleted Successfully!!!</div>');
		redirect('patient/billlist');
	}
	public function delete_uploadoc($id)
	{
		$data = $this->patients->getPatientDocsByid($id);		
		unlink('./uploads/patient/'.$data['doc']);
		$this->patients->deleteuploadData($id);
		$this->session->set_flashdata('msg', '<div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Patient Doc Deleted Successfully!!!</div>');
		redirect('patient/upload_doc');
	}
	public function patientbill(){
		$id=$this->input->get('var1');
		$srno=$this->input->get('var2');
		$data11 = $this->patients->getBillById($id);
		$patient_id=$data11['patient_id'];
		$arr=$this->patients->patient1($patient_id);
		$recieptno=$arr['recieptno'];
		// echo "<pre>";
		// print_r($);
		// exit();
		$deposit=$this->patients->getdepositebyid1($recieptno);
		
		$total = 0;
		foreach ($deposit as $amounts) {
			 $total = $total + $amounts['amount'];
		}
		$arr['deposit']=$total;
		$arr['srno']=$srno;
		// echo "<pre>";
		// print_r($arr);
		// exit();
		$data1['print'] = $data11['print']+1;
		$this->patients->updateBillData($data1,$id);
		$data = $this->patients->getBillById($id);
		$arr['data'] = $data;
		$arr['patientdata'] = $this->patients->getPatientbyId($data['patient_id']);
		$this->load->view('vwPrintBill',$arr);
	}


	public function depositlistprint(){
		$arr['data'] = $this->patients->getAllPatientDeposite();
		// print_r($arr);
		// exit();
		$this->load->view('vwDepositListPrint',$arr);
	}
	public function billlistprint(){
		 $data= $this->patients->getAllPatientBill();
          $depositelist = $this->patients->SearchPatientBillList1();
		$this->load->library('Excel');
		$object = new PHPExcel;
		$object->setActiveSheetIndex(0);
		$object->getActiveSheet()->setCellValue('A1', 'Bill No.');
        $object->getActiveSheet()->mergeCells('A1:A2');
        $object->getActiveSheet()->setCellValue('B1', 'Date');
        $object->getActiveSheet()->mergeCells('B1:B2');
        $object->getActiveSheet()->setCellValue('C1', 'Name');
        $object->getActiveSheet()->mergeCells('C1:C2');
    
		$table_columns= array("Consultant Charge","ICU Charge","Room Charge","Nursing Charge","House Keeping Charge","Treatment Charge","Critical Care Charge","MLC Charge","Oxygen Charge","Ventilator Charge","ECG Charge","Nebulisation Charge","Infusion Pump Charge","B. T. Charge","Waterbed Charge","Dialysis Charge","D.C Shok / CPR Charge","Gastric Lavage Charge","Procedure Charge","Ing Charge","RT","Urinary Catheter","Endotrachcal Intubation","CVP / TLC","TStomy","Tapping/LP","Dressing","Thrombolytic Charge","ABG Charge","Syringe Charge","ICD","");
		$column='D';
		foreach ($table_columns as $field) {
			$object->getActiveSheet()->setCellValue($column.'1', $field);
		    $mergeRange = $column.'1:';
		    $object->getActiveSheet()->setCellValue($column.'2', 'Qty');
		    $column++;
		    $mergeRange = $column.'1:';
		    $object->getActiveSheet()->setCellValue($column.'2', 'Rate');
		    $column++;
		    $mergeRange .= $column.'1';
		    $object->getActiveSheet()->setCellValue($column.'2', 'Amount');
		    $object->getActiveSheet()->mergeCells($mergeRange);
		    $column++;
            }

           $object->getActiveSheet()->setCellValue('CS1', 'Total Amount');
           $object->getActiveSheet()->mergeCells('CS1:CS2');
           $object->getActiveSheet()->setCellValue('CT1', 'Deposit');
           $object->getActiveSheet()->mergeCells('CT1:CT2');   
           $object->getActiveSheet()->setCellValue('CU1', 'Concession');
           $object->getActiveSheet()->mergeCells('CU1:CU2');
           $object->getActiveSheet()->setCellValue('CV1', 'Final Payable Amount');
           $object->getActiveSheet()->mergeCells('CV1:CV2');
          
          $from = "A1"; // or any value
          $to = "CV1"; // or any value
          $object->getActiveSheet()->getStyle("$from:$to")->getFont()->setBold( true );
         $excel_row = 3;
         foreach ($data as $row) {
         	 $qty=json_decode($row->qty);
             $rate=json_decode($row->rate);
             $amount=json_decode($row->amount);
             $ddd=array_sum($amount);
              $to=0;
                foreach ($depositelist as $key) {
                    if ($row->recieptno == $key->ptn_id) {
                        $to += $key->amount;
                    }
                 } 
                 $a=$to;
         	$object->getActiveSheet()->setCellValueByColumnAndRow(0 , $excel_row , $row->bill_no);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(1 , $excel_row , $row->admissiondate."To ". $row->dischargedate);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(2 , $excel_row , $row->name);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(3 , $excel_row , $qty[0]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(4 , $excel_row , $rate[0]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(5 , $excel_row , $amount[0]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(6 , $excel_row , $qty[1]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(7 , $excel_row , $rate[1]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(8 , $excel_row , $amount[1]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(9 , $excel_row , $qty[2]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(10 , $excel_row , $rate[2]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(11, $excel_row , $amount[2]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(12, $excel_row , $qty[3]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(13, $excel_row , $rate[3]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(14, $excel_row , $amount[3]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(15, $excel_row , $qty[4]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(16, $excel_row , $rate[4]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(17, $excel_row , $amount[4]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(18, $excel_row , $qty[5]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(19, $excel_row , $rate[5]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(20, $excel_row , $amount[5]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(21, $excel_row , $qty[6]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(22, $excel_row , $rate[6]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(23, $excel_row , $amount[6]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(24, $excel_row , $qty[7]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(25, $excel_row , $rate[7]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(26, $excel_row , $amount[7]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(27, $excel_row , $qty[8]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(28, $excel_row , $rate[8]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(29, $excel_row , $amount[8]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(30, $excel_row , $qty[9]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(31, $excel_row , $rate[9]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(32, $excel_row , $amount[9]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(33, $excel_row , $qty[10]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(34, $excel_row , $rate[10]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(35, $excel_row , $amount[10]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(36, $excel_row , $qty[11]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(37, $excel_row , $rate[11]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(38, $excel_row , $amount[11]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(39, $excel_row , $qty[12]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(40, $excel_row , $rate[12]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(41, $excel_row , $amount[12]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(42, $excel_row , $qty[13]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(43, $excel_row , $rate[13]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(44, $excel_row , $amount[13]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(45, $excel_row , $qty[14]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(46, $excel_row , $rate[14]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(47, $excel_row , $amount[14]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(48, $excel_row , $qty[15]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(49, $excel_row , $rate[15]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(50, $excel_row , $amount[15]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(51, $excel_row , $qty[16]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(52, $excel_row , $rate[16]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(53, $excel_row , $amount[16]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(54, $excel_row , $qty[17]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(55, $excel_row , $rate[17]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(56, $excel_row , $amount[17]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(57, $excel_row , $qty[18]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(58, $excel_row , $rate[18]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(59, $excel_row , $amount[18]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(60, $excel_row , $qty[19]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(61, $excel_row , $rate[19]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(62, $excel_row , $amount[19]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(63, $excel_row , $qty[20]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(64, $excel_row , $rate[20]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(65, $excel_row , $amount[20]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(66, $excel_row , $qty[21]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(67, $excel_row , $rate[21]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(68, $excel_row , $amount[21]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(69, $excel_row , $qty[22]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(70, $excel_row , $rate[22]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(71, $excel_row , $amount[22]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(72, $excel_row , $qty[23]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(73, $excel_row , $rate[23]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(74, $excel_row , $amount[23]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(75, $excel_row , $qty[24]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(76, $excel_row , $rate[24]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(77, $excel_row , $amount[24]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(78, $excel_row , $qty[25]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(79, $excel_row , $rate[25]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(80, $excel_row , $amount[25]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(81, $excel_row , $qty[26]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(82, $excel_row , $rate[26]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(83, $excel_row , $amount[26]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(84, $excel_row , $qty[27]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(85, $excel_row , $rate[27]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(86, $excel_row , $amount[27]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(87, $excel_row , $qty[28]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(88, $excel_row , $rate[28]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(89, $excel_row , $amount[28]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(90, $excel_row , $qty[29]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(91, $excel_row , $rate[29]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(92, $excel_row , $amount[29]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(93, $excel_row , $qty[30]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(94, $excel_row , $rate[30]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(95, $excel_row , $amount[30]);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(96, $excel_row , $ddd);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(97, $excel_row , $a);
         	$object->getActiveSheet()->setCellValueByColumnAndRow(98, $excel_row , $row->discountamount);
         	$disc= $row->discountamount;
         	$data= $ddd - $a - $disc;
         	$object->getActiveSheet()->setCellValueByColumnAndRow(99, $excel_row , $data);
         	$excel_row++;
         }
         
         $object_writer = PHPExcel_IOFactory::createWriter($object,'Excel5');
         header('Content-Type: application/vnd.ms-excel');
         header('Content-Disposition: attachment;filename="BillData.xls"');
         $object_writer->save('php://output');
		// $arr['data'] = $this->patients->getAllPatientBill();
		// $this->load->view('vwBillListPrint',$arr);
	}


	public function certificate(){
		$patientid=$this->input->get('var1');
		$srno=$this->input->get('var2');
		$arr['patientid']=$patientid;
		$arr['srno']=$srno;
		$arr['patientdata'] = $this->patients->getAllPatient();

		//$arr['patientid']=$patientid;
		$arr['ptndata']=$this->patients->getPatientbyId1($patientid);
		// print_r($arr);
		// exit();	
		//$arr['type'] = 'hide';
		if($this->input->post('save')){
			$arr['patientid']=$this->input->post('patientid');
		    $arr['srno']=$this->input->post('srno');
			$arr['advised']=$this->input->post('advised');
			$name = $this->input->post('name');
			$arr['date'] = $this->input->post('date');
			$arr['age'] = $this->input->post('age');
			$arr['inchargedr'] = $this->input->post('inchargedr');
			$arr['diagnosis'] = $this->input->post('diagnosis');
			$arr['patientid']=$name;
			$data=$this->patients->getPatientbyId($name);
			$arr['name']=$data['name'];
			$arr['patienid2']=$patientid;
			$this->load->view('vwCertificate',$arr);
		}else{
			$this->load->view('vwCertificateForm',$arr);
		}	
	}
	public function indoor_data($patientid){
		$data['is_deleted']=1;
		$this->patients->updatepatientdata($data,$patientid);		
		$data1=$this->patients->getPatientbyId($patientid);
		$Ddata['patient_id']=$patientid;
		$Ddata['patient_address']=$data1['address'];
                $Ddata['ptn_name']=$data1['name'];
		$Ddata['ptn_age']=$data1['age'];
		$Ddata['incharge_dr']=$data1['incharge_dr'];
		$Ddata['diagnosis']=$data1['diagnosis'];
		$Ddata['doa']=$data1['admissiondate'];
		$Ddata['toa']=$data1['admissiontime'];
		$Ddata['dod']=$data1['dischargedate'];
		$Ddata['tod']=$data1['dischargetime'];
		$Ddata['doo']=$data1['doodate'];
		$Ddata['too']=$data1['dootime'];
		$Ddata['mlc']=$data1['mlc'];
		$Ddata['door_no']=$patientid;
		$Ddata['discharge_date']=$data1['dischargedate'];
		$this->patients->insertdischargedata($Ddata);
		redirect('patient/dischargepatient');
	}
	public function dischargepatient() {
		$arr['data'] = $this->patients->getAllDischargePatient();		
		$this->load->view('vwDischargePatient',$arr);			
    }
	public function patientDetailAjax() {
		$patientid = $this->input->post('id');
		$arr['data']=$this->patients->getPatientbyId($patientid);
		$this->load->view('PatientDetailAjax',$arr);
	}
	public function uploadocAjax() {
		$patientid = $this->input->post('id');
		$arr['data']=$this->patients->getPatientbyId($patientid);
		$arr['id']=$patientid;
		if($this->input->post('submit')){ 
			if (count($_FILES['docs']['name'])>0)
			{
				$ptnid = $this->input->post('id');
				if (!is_dir('uploads/patient/')) {
					mkdir('./uploads/patient/', 0777, TRUE);
				}
				$config['upload_path']          = './uploads/patient/';
				$config['allowed_types']        = '*';
				$this->load->library('upload', $config);
				for($i=0;$i<count($_FILES['docs']['name']);$i++){
					$_FILES['attachment']['name']= $_FILES['docs']['name'][$i];
					$_FILES['attachment']['type']= $_FILES['docs']['type'][$i];
					$_FILES['attachment']['tmp_name']= $_FILES['docs']['tmp_name'][$i];
					$_FILES['attachment']['size']= $_FILES['docs']['size'][$i];
					if(!$this->upload->do_upload('attachment')){	
						$err = $this->upload->display_errors();
					}else{
						
						$logodata1 = $this->upload->data();	
						$data['doc'] = $logodata1['file_name'];
						$data['ptn_id'] = $ptnid;
						$this->patients->insertdocsdata($data);
					}
					
				}										
				
							
			}
			
				redirect('patient/upload_doc');
		}else{
			$this->load->view('UploadDocAjax',$arr);
		}		
	}
	public function patientDischargeDetailAjax() {
		$arr['patientid']=$this->input->get('var1');
		$arr['srno']=$this->input->get('var2');
		$dischargeid = $this->input->post('id');
		$arr['data']=$this->patients->getDischargePatientbyId($dischargeid);
		$this->load->view('PatientDischargeDetailAjax',$arr);
	}
	public function retun_patient($patientid) {
		$data['is_deleted']=0;
		$this->patients->updatepatientdata($data,$patientid);
		$dischargedata=$this->patients->getPatientdischargeno($patientid);
		$this->patients->deletedischargedata($dischargedata['discharge_id']);
		redirect('patient/current_patient');
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
