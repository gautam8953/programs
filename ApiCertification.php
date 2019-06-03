<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class ApiCertification extends CI_Controller {
	public function __construct()
	{
		parent :: __construct();
		$this->load->model('FacilityModel');
        $this->load->model('CertificationModel');
		$this->load->model('EmailModel');
	}
	
	public function index(){

	}

	public function getSearchData(){
		if ($this->CommonModel->checkAPIWebUser()) {
		$searchData=$this->input->post();
		$searchData['RoleName']=$this->session->userdata('RoleName');
        if($this->session->userdata('RoleName')=='State'){
            $searchData['cond']=array('mappedField'=>'f.StateID','mappedData'=>$this->session->userdata('MappedState'));
        }
        if($this->session->userdata('RoleName')=='District'){
            $searchData['cond']=array('mappedField'=>'f.DistrictID','mappedData'=>$this->session->userdata('MappedDistrict'));
        }
        if($this->session->userdata('RoleName')=='Facility'){
            $searchData['cond']=array('mappedField'=>'um.UserID','mappedData'=>$this->session->userdata('UserID'));
        }        
		$data=$this->CertificationModel->getSearchData($searchData);
		$json_data=array(
		    "draw"              =>  intval($searchData['draw']),
		    "recordsTotal"      =>  intval($data['totalData']),
		    "recordsFiltered"   =>  intval($data['totalFilter']),
		    "data"              =>  $data['data']
		);
		echo json_encode($json_data);
	} else {
		$json_data=array(
		    "draw"              =>  0,
		    "recordsTotal"      =>  0,
		    "recordsFiltered"   =>  0,
		    "data"              =>  array()
		);
		echo json_encode($json_data);		
	}
	}
	public function getApprovalData(){
		if ($this->CommonModel->checkAPIWebUser()) {
		$searchData=$this->input->post();
		$searchData['RoleName']=$this->session->userdata('RoleName');
        if($this->session->userdata('RoleName')=='State'){
            $searchData['cond']=array('mappedField'=>'f.StateID','mappedData'=>$this->session->userdata('MappedState'));
        }
        if($this->session->userdata('RoleName')=='District'){
            $searchData['cond']=array('mappedField'=>'f.DistrictID','mappedData'=>$this->session->userdata('MappedDistrict'));
        }
        if($this->session->userdata('RoleName')=='Facility'){
            $searchData['cond']=array('mappedField'=>'um.UserID','mappedData'=>$this->session->userdata('UserID'));
        }        
		$data=$this->CertificationModel->getApprovalData($searchData);
		$json_data=array(
		    "draw"              =>  intval($searchData['draw']),
		    "recordsTotal"      =>  intval($data['totalData']),
		    "recordsFiltered"   =>  intval($data['totalFilter']),
		    "data"              =>  $data['data']
		);
		echo json_encode($json_data);
	} else {
		$json_data=array(
		    "draw"              =>  0,
		    "recordsTotal"      =>  0,
		    "recordsFiltered"   =>  0,
		    "data"              =>  array()
		);
		echo json_encode($json_data);		
	}
	}
    function save(){
        if ($this->CommonModel->checkAPIWebUser()) {
        $response = array();
        $dataHeader = apache_request_headers();
        $dataHeader = array_change_key_case($dataHeader, CASE_LOWER);
        if (isset($dataHeader['device']) || isset($dataHeader['token'])) {
            if ($this->input->method(true) == 'POST') {
                if ($this->CommonModel->checkAppRequest($dataHeader)) {
                    // get data and process api requrest
                    $data1 = $this->CommonModel->getApiData();
                } else {
                    $response['code'] = '5';
                    $response['msg'] = $this->config->item('errCodes')[5];
                    $response['APIKey']=$this->session->userdata('APIKey'); 
                    echo json_encode($response);
                    exit;
                }
            } else {
                $response['code'] = '8';
                $response['msg'] = $this->config->item('errCodes')[8];
                $response['APIKey']=$this->session->userdata('APIKey'); 
                echo json_encode($response);
                exit;
            }
        } else {
            if ($this->input->method(true) == 'POST') {
                if ($this->CommonModel->checkAPIWebUser()) {
                    // get data and process web requrest
                    $facilityUser=empty($this->input->post('facilityUser'))?'':encryptor($this->input->post('facilityUser'),'decrypt');
                    $level=empty($this->input->post('level'))?'':encryptor($this->input->post('level'),'decrypt');
                    $type=empty($this->input->post('type'))?'':encryptor($this->input->post('type'),'decrypt');
                    $CertificationID=empty($this->input->post('CertificationID'))?'':encryptor($this->input->post('CertificationID'),'decrypt');

                    if(strtolower($level)=='state'){
                        $level=2;
                    }
                    if(strtolower($level)=='national'){
                        $level=1;
                    }
                    if(!empty($facilityUser) && !empty($level) && !empty($type) ){
                        $data['userID']=$facilityUser;
                        $data['level']=$level;
                        $data['type']=$type;
                        $data['CertificationID']=$CertificationID;
                        $data['user']=$this->session->userdata('UserID');
                        $data['FacilityName']=$this->input->post('FacilityName');
                        if($facilityUser!=$this->session->userdata('UserID')){
                            $data['ParentUserID']=$this->session->userdata('UserID');
                        }
                        if($this->input->post('saveType')=='all'){
                            $data['main']['certificationStatus']=1;
                        } else {
                            $data['main']['certificationStatus']=0;
                        }
                        if(empty($this->input->post('CertificationID'))){
                            $data['main']['certification_no']=$this->input->post('certification_no');
                        }                        
                        $data['main']['FacilityName']=$this->input->post('FacilityName');
                        $data['main']['Address']=$this->input->post('Address');
                        $data['main']['landLine']=$this->input->post('landLine');
                        $data['main']['sqauOfficer']=$this->input->post('sqauOfficer');
                        $data['main']['sqauEmail']=$this->input->post('sqauEmail');
                        $data['main']['sqauTel']=$this->input->post('sqauTel');
                        $data['main']['dqauOfficer']=$this->input->post('dqauOfficer');
                        $data['main']['dqauEmail']=$this->input->post('dqauEmail');
                        $data['main']['dqauTel']=$this->input->post('dqauTel');
                        $data['main']['dqauPeerAssmentScore']=$this->input->post('dqauPeerAssmentScore');
                        $data['main']['dqauPeerAssmentScoreID']=$this->input->post('dqauPeerAssmentScoreID');
                        $data['main']['facilityOfficer']=$this->input->post('facilityOfficer');
                        $data['main']['facilityEmail']=$this->input->post('facilityEmail');
                        $data['main']['facilityTel']=$this->input->post('facilityTel');
                        $data['main']['facilityPeerAssmentScore']=$this->input->post('facilityPeerAssmentScore');
                        $data['main']['railwayStation']=$this->input->post('railwayStation');
                        $data['main']['airport']=$this->input->post('airport');
                        $data['main']['numDeleveries']=$this->input->post('numDeleveries');
                        $data['main']['numCSec']=$this->input->post('numCSec');
                        $data['certification_otherdocID']=$this->input->post('certification_otherdocID');
                        $data['othernameSaved']=$this->input->post('othernameSaved');

                        if(isset($_FILES['appForm'])){
                            if($_FILES['appForm']['size']>0 && $_FILES['appForm']['error']=='' && $_FILES['appForm']['name']!='' ){
                                //if($_FILES['appForm']['size']<900000){
                                    $ext = pathinfo($_FILES['appForm']['name'], PATHINFO_EXTENSION);
                                    $ext=strtolower($ext);
                                    if(in_array($ext,$this->config->item('documents')) ) {
                                      $appForm=date('Y-m-d-H-i-s').preg_replace('/[^A-Za-z0-9\-]/', '', $_FILES['appForm']['name']).'.'.$ext;
                                      if(!is_file('assets/uploads/certification/'.$appForm)){
                                        if(move_uploaded_file($_FILES['appForm']['tmp_name'],"assets/uploads/certification/".$appForm)){
                                            $data['main']['appForm'] = 'assets/uploads/certification/'.$appForm;
                                        } else {
                                            $response['code'] = '4';
                                            $response['msg'] = $this->config->item('errCodes')[4];
                                            echo json_encode($response);
                                            exit;
                                        }
                                        } else {
                                            $response['code'] = '16';
                                            $response['msg'] = 'Please change appForm name';
                                            echo json_encode($response);
                                            exit;
                                        }
                                    } else {
                                        $response['code'] = '16';
                                        $response['msg'] = 'Please uplaod only documents files in appForm';
                                        echo json_encode($response);
                                        exit;
                                    }

                                /*} else {
                                    $response['code'] = '16';
                                    $response['msg'] = 'documents file size should less than 900000 KB in appForm';
                                    echo json_encode($response);
                                    exit;
                                }*/
                            }
                        }
                        if(isset($_FILES['lrsop'])){
                            if($_FILES['lrsop']['size']>0 && $_FILES['lrsop']['error']=='' && $_FILES['lrsop']['name']!='' ){
                                //if($_FILES['lrsop']['size']<900000){
                                    $ext = pathinfo($_FILES['lrsop']['name'], PATHINFO_EXTENSION);
                                    $ext=strtolower($ext);
                                    if(in_array($ext,$this->config->item('documents')) ) {
                                      $lrsop=date('Y-m-d-H-i-s').preg_replace('/[^A-Za-z0-9\-]/', '', $_FILES['lrsop']['name']).'.'.$ext;
                                      if(!is_file('assets/uploads/certification/'.$lrsop)){
                                        if(move_uploaded_file($_FILES['lrsop']['tmp_name'],"assets/uploads/certification/".$lrsop)){
                                            $data['main']['lrsop'] = 'assets/uploads/certification/'.$lrsop;
                                        } else {
                                            $response['code'] = '4';
                                            $response['msg'] = $this->config->item('errCodes')[4];
                                            echo json_encode($response);
                                            exit;
                                        }
                                        } else {
                                            $response['code'] = '16';
                                            $response['msg'] = 'Please change documents name in lrsop';
                                            echo json_encode($response);
                                            exit;
                                        }
                                    } else {
                                        $response['code'] = '16';
                                        $response['msg'] = 'Please uplaod only documents files in lrsop';
                                        echo json_encode($response);
                                        exit;
                                    }

                                /*} else {
                                    $response['code'] = '16';
                                    $response['msg'] = 'documents file size should less than 900000 KB in lrsop';
                                    echo json_encode($response);
                                    exit;
                                }*/
                            }
                        }
                        if(isset($_FILES['otsop'])){
                            if($_FILES['otsop']['size']>0 && $_FILES['otsop']['error']=='' && $_FILES['otsop']['name']!='' ){
                                //if($_FILES['otsop']['size']<900000){
                                    $ext = pathinfo($_FILES['otsop']['name'], PATHINFO_EXTENSION);
                                    $ext=strtolower($ext);
                                    if(in_array($ext,$this->config->item('documents')) ) {
                                      $otsop=date('Y-m-d-H-i-s').preg_replace('/[^A-Za-z0-9\-]/', '', $_FILES['otsop']['name']).'.'.$ext;
                                      if(!is_file('assets/uploads/certification/'.$otsop)){
                                        if(move_uploaded_file($_FILES['otsop']['tmp_name'],"assets/uploads/certification/".$otsop)){
                                            $data['main']['otsop'] = 'assets/uploads/certification/'.$otsop;
                                        } else {
                                            $response['code'] = '4';
                                            $response['msg'] = $this->config->item('errCodes')[4];
                                            echo json_encode($response);
                                            exit;
                                        }
                                        } else {
                                            $response['code'] = '16';
                                            $response['msg'] = 'Please change documents name in otsop';
                                            echo json_encode($response);
                                            exit;
                                        }
                                    } else {
                                        $response['code'] = '16';
                                        $response['msg'] = 'Please uplaod only documents files in otsop';
                                        echo json_encode($response);
                                        exit;
                                    }

                                /*} else {
                                    $response['code'] = '16';
                                    $response['msg'] = 'documents file size should less than 900000 KB in otsop';
                                    echo json_encode($response);
                                    exit;
                                }*/
                            }
                        }
                        if(!empty($this->input->post('anexturedate'))){
                            foreach ($this->input->post('anexturedate') as $key => $value) {
                                $uploaded=0;
                                if(isset($_FILES['anexturedoc']['size'][$key]) && $_FILES['anexturedoc']['size'][$key]>0 && $_FILES['anexturedoc']['error'][$key]=='' && $_FILES['anexturedoc']['name'][$key]!='' ){
                                        $ext = pathinfo($_FILES['anexturedoc']['name'][$key], PATHINFO_EXTENSION);
                                        $ext=strtolower($ext);
                                        if(in_array($ext,$this->config->item('ExcelTypes')) ) {
                                          $anexturedoc=date('Y-m-d-H-i-s').preg_replace('/[^A-Za-z0-9\-]/', '', $_FILES['anexturedoc']['name'][$key]).'.'.$ext;
                                          if(!is_file('assets/uploads/certification/'.$anexturedoc)){
                                            if(move_uploaded_file($_FILES['anexturedoc']['tmp_name'][$key],"assets/uploads/certification/".$anexturedoc)){
                                                $uploaded=1;
                                            } else {
                                                $response['code'] = '4';
                                                $response['msg'] = $this->config->item('errCodes')[4];
                                                echo json_encode($response);
                                                exit;
                                            }
                                            } else {
                                                $response['code'] = '16';
                                                $response['msg'] = 'Please change anexture document name ';
                                                echo json_encode($response);
                                                exit;
                                            }
                                        } else {
                                            $response['code'] = '16';
                                            $response['msg'] = 'Please uplaod only excel files in anextures ';
                                            echo json_encode($response);
                                            exit;
                                        }

                                }
                                $anextureArr=array('submitdate'=>convert_date_db($this->input->post('anexturedate')[$key]),'monthseq'=>$key);
                                if($uploaded==1){
                                    $anextureArr['submitfile']='assets/uploads/certification/'.$anexturedoc;
                                }
                                $data['anexturedoc'][] = $anextureArr;
                            }

                        }
                        if(isset($_FILES['otherdoc'])){
                            foreach ($_FILES['otherdoc']['name'] as $key => $value) {
                                if($_FILES['otherdoc']['size'][$key]>0 && $_FILES['otherdoc']['error'][$key]=='' && $_FILES['otherdoc']['name'][$key]!='' ){
                                    //if($_FILES['otherdoc']['size'][$key]<900000){
                                        $ext = pathinfo($_FILES['otherdoc']['name'][$key], PATHINFO_EXTENSION);
                                        $ext=strtolower($ext);
                                        if(in_array($ext,$this->config->item('documents')) ) {
                                          $otherdoc=date('Y-m-d-H-i-s').preg_replace('/[^A-Za-z0-9\-]/', '', $_FILES['otherdoc']['name'][$key]).'.'.$ext;
                                          if(!is_file('assets/uploads/certification/'.$otherdoc)){
                                            if(move_uploaded_file($_FILES['otherdoc']['tmp_name'][$key],"assets/uploads/certification/".$otherdoc)){
                                                $data['otherdoc'][] = array('doc'=>'assets/uploads/certification/'.$otherdoc,'name'=>$this->input->post('othername')[$key]);
                                            } else {
                                                $response['code'] = '4';
                                                $response['msg'] = $this->config->item('errCodes')[4];
                                                echo json_encode($response);
                                                exit;
                                            }
                                            } else {
                                                $response['code'] = '16';
                                                $response['msg'] = 'Please change documents name in otherdoc ';
                                                echo json_encode($response);
                                                exit;
                                            }
                                        } else {
                                            $response['code'] = '16';
                                            $response['msg'] = 'Please uplaod only documents files in otherdoc ';
                                            echo json_encode($response);
                                            exit;
                                        }

                                    /*} else {
                                        $response['code'] = '16';
                                        $response['msg'] = 'documents file size should less than 900000 KB in otherdoc '.$key+1;
                                        echo json_encode($response);
                                        exit;
                                    }*/
                                }
                            }

                        }
        //echo "<pre>"; print_r($_FILES); echo "</pre>";
        //echo "<pre>"; print_r($_REQUEST); echo "</pre>";
                        $response=$this->CertificationModel->save($data);
                        echo json_encode($response);
                        exit;
                    } else {
                        $response['code'] = '9';
                        $response['msg'] = $this->config->item('errCodes')[9];
                        echo json_encode($response);
                        exit;
                    }

                } else {
                    redirect('/');
                }
            } else {
                redirect('/');
            }
        }
    }

    }
	function savestatus(){
		if ($this->CommonModel->checkAPIWebUser()) {
        $response = array();
        $dataHeader = apache_request_headers();
        $dataHeader = array_change_key_case($dataHeader, CASE_LOWER);
        if (isset($dataHeader['device']) || isset($dataHeader['token'])) {
            if ($this->input->method(true) == 'POST') {
                if ($this->CommonModel->checkAppRequest($dataHeader)) {
                    // get data and process api requrest
                    $data1 = $this->CommonModel->getApiData();
                } else {
                    $response['code'] = '5';
                    $response['msg'] = $this->config->item('errCodes')[5];
                    $response['APIKey']=$this->session->userdata('APIKey'); 
                    echo json_encode($response);
                    exit;
                }
            } else {
                $response['code'] = '8';
                $response['msg'] = $this->config->item('errCodes')[8];
                $response['APIKey']=$this->session->userdata('APIKey'); 
                echo json_encode($response);
                exit;
            }
        } else {
            if ($this->input->method(true) == 'POST') {
                if ($this->CommonModel->checkAPIWebUser()) {
                    // get data and process web requrest
                	$CertificationID=empty($this->input->post('CertificationID'))?'':encryptor($this->input->post('CertificationID'),'decrypt');
                    if(!empty($CertificationID) ){
                        $data['CertificationID']=$CertificationID;
                        $data['user']=$this->session->userdata('UserID');
                        $data['main']['remarks']=$this->input->post('remarks');
                        $data['main']['status']=$this->input->post('status');
                        if(isset($_FILES['docCertificate'])){
                            if($_FILES['docCertificate']['size']>0 && $_FILES['docCertificate']['error']=='' && $_FILES['docCertificate']['name']!='' ){
                                if($_FILES['docCertificate']['size']<900000){
                                    $ext = pathinfo($_FILES['docCertificate']['name'], PATHINFO_EXTENSION);
                                    $ext=strtolower($ext);
                                    if(in_array($ext,$this->config->item('documents')) ) {
                                      $docCertificate=date('Y-m-d-H-i').preg_replace('/[^A-Za-z0-9\-]/', '', $_FILES['docCertificate']['name']).'.'.$ext;
                                      if(!is_file('assets/uploads/certification/'.$docCertificate)){
                                        if(move_uploaded_file($_FILES['docCertificate']['tmp_name'],"assets/uploads/certification/".$docCertificate)){
                                            $data['main']['docCertificate'] = 'assets/uploads/certification/'.$docCertificate;
                                        } else {
                                            $response['code'] = '4';
                                            $response['msg'] = $this->config->item('errCodes')[4];
                                            echo json_encode($response);
                                            exit;
                                        }
                                        } else {
                                            $response['code'] = '16';
                                            $response['msg'] = 'Please change documents name in docCertificate';
                                            echo json_encode($response);
                                            exit;
                                        }
                                    } else {
                                        $response['code'] = '16';
                                        $response['msg'] = 'Please uplaod only documents files in docCertificate';
                                        echo json_encode($response);
                                        exit;
                                    }

                                } else {
                                    $response['code'] = '16';
                                    $response['msg'] = 'documents file size should less than 900000 KB in docCertificate';
                                    echo json_encode($response);
                                    exit;
                                }
                            }
                        }


        //echo "<pre>"; print_r($_FILES); echo "</pre>";
        //echo "<pre>"; print_r($_REQUEST); echo "</pre>";
                        //echo "<pre>"; print_r($data); echo "</pre>";
                        $response=$this->CertificationModel->savestatus($data);
                        echo json_encode($response);
						exit;
                    } else {
                        $response['code'] = '9';
                        $response['msg'] = $this->config->item('errCodes')[9];
						echo json_encode($response);
						exit;
					}

                } else {
                    redirect('/');
                }
            } else {
                redirect('/');
            }
        }
	}

	}

}