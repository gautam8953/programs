<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Certification extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $userID = $this->session->userdata('UserID');
        if (empty($userID)) {
            redirect('user/login');
        }
        $this->load->model('FacilityModel');
        $this->load->model('CertificationModel');
        $this->load->model('MonthlyModel');
    }

  	public function index(){
      // certification apply listing page
      $this->CommonModel->checkPageAccessWeb('certification/index',$this->session->userdata('RoleName'));
      $data=array();
      $data['search_options']=$this->FacilityModel->getSearchOptions_new();
      $this->load->view('header');
      $this->load->view('certification/index',$data);
      $this->load->view('footer');

  	}
    public function apply($user='',$level='',$type='',$CertificationID=''){
      // apply/edit certification page
      $user=encryptor($user,'decrypt'); 
      $level=encryptor($level,'decrypt'); 
      $type=encryptor($type,'decrypt');
      $CertificationID=encryptor($CertificationID,'decrypt');
      if(empty($user) || empty($level) || empty($type) || !in_array($type,array('lr','ot','both')) || !in_array($level,array('State','National'))  ){
        redirect('/');
      } else {
        $data=array();
        $data['user']=$user;
        $data['level']=$level;
        $data['type']=$type;
        $data['CertificationID']=$CertificationID;
        if(empty($CertificationID)){
          // add link
          if(!$this->CommonModel->checkPageActionWeb('certification/index','access_add',$this->session->userdata('RoleName'))){
              redirect('/');
          }
        } else {
          // edit link
          if(!$this->CommonModel->checkPageActionWeb('certification/index','access_edit',$this->session->userdata('RoleName'))){
              redirect('/');
          }
        }
/*        if(!empty($CertificationID)){
          $data['data']=$this->CertificationModel->get_certification_data($CertificationID);          
        }*/
        $data['data']=$this->CertificationModel->get_certification_data($CertificationID,$user);
        //echo "<pre>"; print_r($data); echo "</pre>";
        $this->load->view('header');
        $this->load->view('certification/apply',$data);
        $this->load->view('footer');
      }
    }
    public function downloadFormat($user,$type){
      $user=encryptor($user,'decrypt');
      $type=encryptor($type,'decrypt');
      if(empty($user) || empty($user)){
        redirect('/');
      } else {
        $data=array();
        $data['user']=$user;
        $data['type']=$type;
        $data['data']=$this->CertificationModel->getdata($user);
        $this->load->view('certification/downloadFormat',$data);
      }
    }
    public function approval(){
      // certification approval Listing page
      $this->CommonModel->checkPageAccessWeb('certification/approval',$this->session->userdata('RoleName'));
      $data=array();
      $data['search_options']=$this->FacilityModel->getSearchOptions_new();
      $this->load->view('header');
      $this->load->view('certification/approval',$data);
      $this->load->view('footer');

    }
    public function certificationview($CertificationID=''){
      $data=array();
      $CertificationID=encryptor($CertificationID,'decrypt');
      if(empty($CertificationID)){
        redirect('/');
      } else {
        if(!$this->CommonModel->checkPageActionWeb('certification/index','access_view',$this->session->userdata('RoleName'))){
            redirect('/');
        }
        $data['data']=$this->CertificationModel->get_certification_dataview($CertificationID);
        $data['CertificationID']=$CertificationID;
        $data['search_options']=$this->FacilityModel->getSearchOptions_new();
        //echo "<pre>"; print_r($data); echo "</pre>";
        $this->load->view('header');
        $this->load->view('certification/certificationview',$data);
        $this->load->view('footer');        
      }

    }
    public function approvaledit($CertificationID=''){
      $data=array();
      $CertificationID=encryptor($CertificationID,'decrypt');
      if(empty($CertificationID)){
        redirect('/');
      } else {
        if(!$this->CommonModel->checkPageActionWeb('certification/approval','access_edit',$this->session->userdata('RoleName'))){
            redirect('/');
        }
        $data['data']=$this->CertificationModel->get_approvaledit($CertificationID);
        $data['CertificationID']=$CertificationID;
        $data['search_options']=$this->FacilityModel->getSearchOptions_new();
        //echo "<pre>"; print_r($data); echo "</pre>";
        $this->load->view('header');
        $this->load->view('certification/approvaledit',$data);
        $this->load->view('footer');        
      }

    }
    public function approvalview($CertificationID=''){
      $data=array();
      $CertificationID=encryptor($CertificationID,'decrypt');
      if(empty($CertificationID)){
        redirect('/');
      } else {
        if(!$this->CommonModel->checkPageActionWeb('certification/approval','access_view',$this->session->userdata('RoleName'))){
            redirect('/');
        }
        $data['data']=$this->CertificationModel->get_approvalview($CertificationID);
        $data['CertificationID']=$CertificationID;
        $data['search_options']=$this->FacilityModel->getSearchOptions_new();
        //echo "<pre>"; print_r($data); echo "</pre>";
        $this->load->view('header');
        $this->load->view('certification/approvalview',$data);
        $this->load->view('footer');        
      }

    }
    function hospitaldata($CertificationID=''){
      $data=array();
      $CertificationID=encryptor($CertificationID,'decrypt');
      if(empty($CertificationID)){
        redirect('/');
      } else {
        $data['data']=$this->CertificationModel->get_approvalview($CertificationID);
        $data['CertificationID']=$CertificationID;
        $this->load->view('header');
        $this->load->view('certification/hospitaldata',$data);
        $this->load->view('footer');        
      }

    }
    function hospitaldata_pdf($CertificationID=''){
      $this->load->library('Pdf');
      $html='';
      $data=array();
      $CertificationID=encryptor($CertificationID,'decrypt');
      if(empty($CertificationID)){
        redirect('/');
      } else {
        $data['data']=$this->CertificationModel->get_approvalview($CertificationID);
        $data['CertificationID']=$CertificationID;
        //$this->load->view('certification/hospitaldata_pdf',$data);
        $html.=$this->load->view('certification/hospitaldata_pdf',$data,true);
        $pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetTitle('Hospital Data Sheet');
        $pdf->SetHeaderMargin(20);
                $pdf->SetTopMargin(0); 
                $pdf->setFooterMargin(20);
                $pdf->SetAutoPageBreak(true);
                $pdf->SetPrintHeader(false);
                $pdf->SetPrintFooter(false);
        $pdf->SetAuthor('Author');
        $pdf->SetDisplayMode('real', 'default');
        $pdf->AddPage();
        $pdf->writeHTML($html, true, false, true, false, '');
        $userFolder=$this->session->userdata('UserID');
        if (!file_exists('assets/pdf/'.$userFolder)) {
            mkdir('assets/pdf/'.$userFolder, 0777, true);
        }        
        $pdf->Output(__DIR__ . '../../../assets/pdf/'.$userFolder.'/hospitaldata.pdf', 'F');
        $this->load->helper('download');
        $file = file_get_contents(base_url()."/assets/pdf/".$userFolder."/hospitaldata.pdf");
        $name = 'hospitaldata.pdf';
        force_download($name, $file);
        echo '<script>window.close()</script>';
      }

    }
   function downloadAll($CertificationID=''){
      $this->load->library('Pdf');
      $html='';
      $data=array();
      $CertificationID=encryptor($CertificationID,'decrypt');
      if(empty($CertificationID)){
        redirect('/');
      } else {
        $fileLinks=array();
        $data['data']=$this->CertificationModel->get_approvaledit($CertificationID);
        $lrAssessmentID=$data['data']['main']['facilityPeerAssmentLR'];
        $lrAssessmentFormat=$data['data']['main']['formatLR'];
        $otAssessmentID=$data['data']['main']['facilityPeerAssmentOT'];
        $otAssessmentFormat=$data['data']['main']['formatOT'];
        // for uploaded files links started
        // Application Form ended
        if(!empty($data['data']['main']['appForm'])){ 
          if(is_file($data['data']['main']['appForm'])){
            $nameArr=explode('/', $data['data']['main']['appForm']);
            $name=end($nameArr);
            $fileLinks[$name]=file_get_contents($data['data']['main']['appForm']);
          }
        }
        // Application Form ended
        // LR sop started
        if(!empty($data['data']['main']['lrsop'])){ 
          if(is_file($data['data']['main']['lrsop'])){
            $nameArr=explode('/', $data['data']['main']['lrsop']);
            $name=end($nameArr);
            $fileLinks[$name]=file_get_contents($data['data']['main']['lrsop']);
          }
        }
        // LR sop ended
        // OT sop started
        if(!empty($data['data']['main']['otsop'])){ 
          if(is_file($data['data']['main']['otsop'])){
            $nameArr=explode('/', $data['data']['main']['otsop']);
            $name=end($nameArr);
            $fileLinks[$name]=file_get_contents($data['data']['main']['otsop']);
          }
        }
        // OT sop ended
        // other doc started
        if(!empty($data['data']['othername'])){ 
          foreach($data['data']['othername'] as $key => $value) {
            $docFileName=empty($value['name'])?'otherDoc_'.$key:$value['name'];
            if(is_file($value['doc'])){
              $nameArr=explode('/', $value['doc']);
              $name=end($nameArr);
              $fileLinks[$name]=file_get_contents($value['doc']);
            }
          } }

        // other doc ended
        // anexture doc started
        if(!empty($data['data']['anexturedoc'])){ 
          foreach($data['data']['anexturedoc'] as $key => $value) {
            $docFileName=empty($value['submitfile'])?'otherDoc_'.$key:$value['submitfile'];
            if(is_file($value['submitfile'])){
              $nameArr=explode('/', $value['submitfile']);
              $name=end($nameArr);
              $fileLinks[$name]=file_get_contents($value['submitfile']);
            }
          } }

        // anexture doc ended

        $html='';
        unset($data['data']);
        // for uploaded files links ended

        // for hospital data sheet started
        $data['data']=$this->CertificationModel->get_approvalview($CertificationID);
        $data['CertificationID']=$CertificationID;
        $html.=$this->load->view('certification/hospitaldata_pdf',$data,true);
        $pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetTitle('Hospital Data');
        $pdf->SetHeaderMargin(20);
                $pdf->SetTopMargin(0); 
                $pdf->setFooterMargin(20);
                $pdf->SetAutoPageBreak(true);
                $pdf->SetPrintHeader(false);
                $pdf->SetPrintFooter(false);
        $pdf->SetAuthor('Author');
        $pdf->SetDisplayMode('real', 'default');
        $pdf->AddPage();
        $pdf->writeHTML($html, true, false, true, false, '');
        $userFolder=$this->session->userdata('UserID');
        if (!file_exists('assets/pdf/'.$userFolder)) {
            mkdir('assets/pdf/'.$userFolder, 0777, true);
        }        
        $pdf->Output(__DIR__ . '../../../assets/pdf/'.$userFolder.'/hospitaldata.pdf', 'F');
        $fileLinks['hospitaldata.pdf']=file_get_contents("assets/pdf/".$userFolder."/hospitaldata.pdf");
        $html='';
        unset($data['data']);
        // for hospital data sheet ended

        // lr assessment started
        if(!empty($lrAssessmentID) && !empty($lrAssessmentFormat)){
          $dataLR=array();
          $dataLR['ansId']=$lrAssessmentID;
          $dataLR['format']=$lrAssessmentFormat;

          $data['data']=$this->FacilityModel->surveyFormatDownload($dataLR);
          $html.=$this->load->view('facility/facility_pdf',$data,true);
          $pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
          $pdf->SetTitle('Facility');
          $pdf->SetHeaderMargin(20);
                $pdf->SetTopMargin(0); 
                $pdf->setFooterMargin(20);
                $pdf->SetAutoPageBreak(true);
                $pdf->SetPrintHeader(false);
                $pdf->SetPrintFooter(false);
          $pdf->SetAuthor('Author');
          $pdf->SetDisplayMode('real', 'default');
          $pdf->AddPage();
          $pdf->writeHTML($html, true, false, true, false, '');
          $userFolder=$this->session->userdata('UserID');
          if (!file_exists('assets/pdf/'.$userFolder)) {
              mkdir('assets/pdf/'.$userFolder, 0777, true);
          }
          if($data['data']['score'][0]['SurveyName']=='Operation Theater'){
            $pdfName='otassessment.pdf';
          } else {
            $pdfName='lrassessment.pdf';
          }
          $pdf->Output(__DIR__ . '../../../assets/pdf/'.$userFolder.'/'.$pdfName, 'F');
          $fileLinks[$pdfName]=file_get_contents("assets/pdf/".$userFolder."/".$pdfName);
          $html='';
          unset($data['data']);
        }
        // lr assessment ended
        // ot assessment started
        if(!empty($otAssessmentID) && !empty($otAssessmentFormat)){
          $dataOT=array();
          $dataOT['ansId']=$otAssessmentID;
          $dataOT['format']=$otAssessmentFormat;

          $data['data']=$this->FacilityModel->surveyFormatDownload($dataOT);
          $html.=$this->load->view('facility/facility_pdf',$data,true);
          $pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
          $pdf->SetTitle('Facility');
          $pdf->SetHeaderMargin(20);
                $pdf->SetTopMargin(0); 
                $pdf->setFooterMargin(20);
                $pdf->SetAutoPageBreak(true);
                $pdf->SetPrintHeader(false);
                $pdf->SetPrintFooter(false);
          $pdf->SetAuthor('Author');
          $pdf->SetDisplayMode('real', 'default');
          $pdf->AddPage();
          $pdf->writeHTML($html, true, false, true, false, '');
          $userFolder=$this->session->userdata('UserID');
          if (!file_exists('assets/pdf/'.$userFolder)) {
              mkdir('assets/pdf/'.$userFolder, 0777, true);
          }
          if($data['data']['score'][0]['SurveyName']=='Operation Theater'){
            $pdfName='otassessment.pdf';
          } else {
            $pdfName='lrassessment.pdf';
          }
          $pdf->Output(__DIR__ . '../../../assets/pdf/'.$userFolder.'/'.$pdfName, 'F');
          $fileLinks[$pdfName]=file_get_contents("assets/pdf/".$userFolder."/".$pdfName);
          $html='';
          unset($data['data']);
        }
        // ot assessment ended
        // indicator 1 started
            $datapdf=array();
            $datapdf['CertificationID']=encryptor($CertificationID);            
            $datapdf['monthCount']=encryptor('1');
            $data['indicator_data']=$this->MonthlyModel->reportData_certificate($datapdf);
            $html.=$this->load->view('monthly/monthly_report_certificate_pdf',$data,true);
            $monthCounts='1';
            $pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
            $pdf->SetTitle('Monthly Report Certificate');
            $pdf->SetHeaderMargin(20);
                $pdf->SetTopMargin(0); 
                $pdf->setFooterMargin(20);
                $pdf->SetAutoPageBreak(true);
                $pdf->SetPrintHeader(false);
                $pdf->SetPrintFooter(false);
            $pdf->SetAuthor('Author');
            $pdf->SetDisplayMode('real', 'default');
            $pdf->AddPage();
            $pdf->writeHTML($html, true, false, true, false, '');
            $userFolder=$this->session->userdata('UserID');
            if (!file_exists('assets/pdf/'.$userFolder)) {
                mkdir('assets/pdf/'.$userFolder, 0777, true);
            }        
            $name = 'indicator_report_'.$monthCounts.'.pdf';
            $pdf->Output(__DIR__ . '../../../assets/pdf/'.$userFolder.'/'.$name, 'F');
            $fileLinks[$name]=file_get_contents("assets/pdf/".$userFolder."/".$name);
            $html='';
            unset($data['indicator_data']);
        // indicator 1 ended
        // indicator 2 started
            $datapdf=array();
            $datapdf['CertificationID']=encryptor($CertificationID);            
            $datapdf['monthCount']=encryptor('2');
            $data['indicator_data']=$this->MonthlyModel->reportData_certificate($datapdf);
            $html.=$this->load->view('monthly/monthly_report_certificate_pdf',$data,true);
            $monthCounts='2';
            $pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
            $pdf->SetTitle('Monthly Report Certificate');
            $pdf->SetHeaderMargin(20);
                $pdf->SetTopMargin(0); 
                $pdf->setFooterMargin(20);
                $pdf->SetAutoPageBreak(true);
                $pdf->SetPrintHeader(false);
                $pdf->SetPrintFooter(false);
            $pdf->SetAuthor('Author');
            $pdf->SetDisplayMode('real', 'default');
            $pdf->AddPage();
            $pdf->writeHTML($html, true, false, true, false, '');
            $userFolder=$this->session->userdata('UserID');
            if (!file_exists('assets/pdf/'.$userFolder)) {
                mkdir('assets/pdf/'.$userFolder, 0777, true);
            }        
            $name = 'indicator_report_'.$monthCounts.'.pdf';
            $pdf->Output(__DIR__ . '../../../assets/pdf/'.$userFolder.'/'.$name, 'F');
            $fileLinks[$name]=file_get_contents("assets/pdf/".$userFolder."/".$name);
            $html='';
            unset($data['indicator_data']);
        // indicator 2 ended
        // indicator 3 started
            $datapdf=array();
            $datapdf['CertificationID']=encryptor($CertificationID);            
            $datapdf['monthCount']=encryptor('3');
            $data['indicator_data']=$this->MonthlyModel->reportData_certificate($datapdf);
            $html.=$this->load->view('monthly/monthly_report_certificate_pdf',$data,true);
            $monthCounts='3';
            $pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
            $pdf->SetTitle('Monthly Report Certificate');
            $pdf->SetHeaderMargin(20);
                $pdf->SetTopMargin(0); 
                $pdf->setFooterMargin(20);
                $pdf->SetAutoPageBreak(true);
                $pdf->SetPrintHeader(false);
                $pdf->SetPrintFooter(false);
            $pdf->SetAuthor('Author');
            $pdf->SetDisplayMode('real', 'default');
            $pdf->AddPage();
            $pdf->writeHTML($html, true, false, true, false, '');
            $userFolder=$this->session->userdata('UserID');
            if (!file_exists('assets/pdf/'.$userFolder)) {
                mkdir('assets/pdf/'.$userFolder, 0777, true);
            }
            $name = 'indicator_report_'.$monthCounts.'.pdf';
            $pdf->Output(__DIR__ . '../../../assets/pdf/'.$userFolder.'/'.$name, 'F');
            $fileLinks[$name]=file_get_contents("assets/pdf/".$userFolder."/".$name);
            $html='';
            unset($data['indicator_data']);
        // indicator 3 ended
          $this->load->library('zip');
          $this->zip->add_data($fileLinks);
          $this->zip->archive('assets/pdf/'.$userFolder.'/certification.zip');
        //echo "<pre>"; print_r($fileLinks); echo "</pre>";        
        $this->zip->download($userFolder.'.zip');
        
        //echo '<script>window.close()</script>';
      }

    }



}