<?php

class CertificationModel extends CI_Model {
	function __construct() {
		parent::__construct();
	}

	function getSearchData($searchData){

        $data = array();
        $col = array(
            0 => 'FacilityName',
        );
	  	$this->db->select('f.facilityName');
	    $this->db->from('answer');
		$this->db->join('usermapping um','answer.UserID=um.UserID');
		$this->db->join('facilities f', 'f.FacilityID=um.FacilityID AND um.FacilityID>0 AND f.IsActive=1', 'inner');
		$this->db->join('states s','f.StateID=s.StateID AND f.IsActive=1', 'inner');
		$this->db->join('district d','f.DistrictID=d.DistrictID AND f.IsActive=1', 'inner');
		if(!empty($searchData['cond'])){
			$this->db->where_in($searchData['cond']['mappedField'],$searchData['cond']['mappedData']);
		}
	    $this->db->where('answer.IsActive','1');
	    $this->db->where('answer.SurveyStatus','1');
        if(!empty($searchData['search']['value'])){
            $searchString=trim($searchData['search']['value']);
            if(strtolower($searchString)=='base line' || strtolower($searchString)=='base' || strtolower($searchString)=='baseline'){ 
                $this->db->where("(answer.Sequence='1')", NULL, FALSE);
            } else if(strtolower($searchString)=='others' || strtolower($searchString)=='other'){ 
                $this->db->where("(answer.Sequence='2')", NULL, FALSE);
            } else if(strtolower($searchString)=='end line' || strtolower($searchString)=='end' || strtolower($searchString)=='endline'){ 
                $this->db->where("(answer.Sequence='3')", NULL, FALSE);
            } else if(strtolower($searchString)=='completed' || strtolower($searchString)=='complete'){ 
                $this->db->where("(answer.SurveyStatus='1')", NULL, FALSE);
            } else if(strtolower($searchString)=='incomplete'){
                $this->db->where("(answer.SurveyStatus='0')", NULL, FALSE);
            } else {
                $this->db->where("(f.FacilityName like '%".$searchString."%' OR d.DistrictName like '%".$searchString."%' OR s.StateName like '%".$searchString."%')", NULL, FALSE);
            }
        }
        if(!empty($searchData['search_state'])){
        	$this->db->where('s.StateID',$searchData['search_state']);
        }
        if(!empty($searchData['search_district'])){
        	$this->db->where('d.DistrictID',$searchData['search_district']);
        }
        if(!empty($searchData['search_facility'])){
        	$this->db->where('answer.UserID',$searchData['search_facility']);
        }
        $this->db->group_by('answer.UserID');
	    $queryTot = $this->db->get();  // total number of record get





	    $this->db->select('f.FacilityName,s.StateName,d.DistrictName,max(answer.AnswerID) as ansId,answer.UserID');
	    $this->db->from('answer');
	    $this->db->join('survey', 'answer.SurveyID = survey.SurveyID', 'inner');

		$this->db->join('usermapping um','answer.UserID=um.UserID');
		$this->db->join('facilities f', 'f.FacilityID=um.FacilityID AND um.FacilityID>0 AND f.IsActive=1', 'inner');
		$this->db->join('states s','f.StateID=s.StateID AND f.IsActive=1', 'inner');
		$this->db->join('district d','f.DistrictID=d.DistrictID AND f.IsActive=1', 'inner');
		if(!empty($searchData['cond'])){
		$this->db->where_in($searchData['cond']['mappedField'],$searchData['cond']['mappedData']);
		}
	    $this->db->where('answer.IsActive','1');
	    $this->db->where('answer.SurveyStatus','1');
        if(!empty($searchData['search']['value'])){
            $searchString=trim($searchData['search']['value']);
            if(strtolower($searchString)=='base line' || strtolower($searchString)=='base' || strtolower($searchString)=='baseline'){ 
                $this->db->where("(answer.Sequence='1')", NULL, FALSE);
            } else if(strtolower($searchString)=='others' || strtolower($searchString)=='other'){ 
                $this->db->where("(answer.Sequence='2')", NULL, FALSE);
            } else if(strtolower($searchString)=='end line' || strtolower($searchString)=='end' || strtolower($searchString)=='endline'){ 
                $this->db->where("(answer.Sequence='3')", NULL, FALSE);
            } else if(strtolower($searchString)=='completed' || strtolower($searchString)=='complete'){ 
                $this->db->where("(answer.SurveyStatus='1')", NULL, FALSE);
            } else if(strtolower($searchString)=='incomplete'){
                $this->db->where("(answer.SurveyStatus='0')", NULL, FALSE);
            } else {
                $this->db->where("(f.FacilityName like '%".$searchString."%' OR d.DistrictName like '%".$searchString."%' OR s.StateName like '%".$searchString."%')", NULL, FALSE);
            }
        }
        if(!empty($searchData['search_state'])){
        	$this->db->where('s.StateID',$searchData['search_state']);
        }
        if(!empty($searchData['search_district'])){
        	$this->db->where('d.DistrictID',$searchData['search_district']);
        }
        if(!empty($searchData['search_facility'])){
        	$this->db->where('answer.UserID',$searchData['search_facility']);
        }
        $this->db->group_by('answer.UserID');
	    $this->db->order_by($col[$searchData['order'][0]['column']], $searchData['order'][0]['dir']);
	    $this->db->limit($this->input->post('length'),$this->input->post('start'));
	    $query = $this->db->get(); // get total record and loop for related data
	    $cnt_data=$searchData['start'];

        $access_approval=$this->CommonModel->checkPageActionWeb('certification/approval','access_edit',$this->session->userdata('RoleName'));
        $access_eligibility=$this->CommonModel->checkPageActionWeb('reports/checklist','access_view',$this->session->userdata('RoleName'));
        $access_add=$this->CommonModel->checkPageActionWeb('certification/index','access_add',$this->session->userdata('RoleName'));
        $access_edit=$this->CommonModel->checkPageActionWeb('certification/index','access_edit',$this->session->userdata('RoleName'));
        //$access_delete=$this->CommonModel->checkPageActionWeb('certification/index','access_delete',$this->session->userdata('RoleName'));
        $access_view=$this->CommonModel->checkPageActionWeb('certification/index','access_view',$this->session->userdata('RoleName'));
		foreach ($query->result_array() as $key => $value) {
			$facilityUrl=trim(str_replace(',','-', $value['FacilityName']));
			++$cnt_data;
			$cnt_data_in=1;
			$sqlIn="SELECT a.answerId,a.surveyID,a.clientScore,c.CategoryCode,s.SubCategoryCode,count(q.QuestionID) as quesTot,sum(ad.Answer) as answer
FROM `answer` as a
INNER JOIN answerdetail ad on(a.AnswerId=ad.AnswerID AND ad.IsActive='1')
INNER JOIN question q on(ad.QuestionID=q.QuestionID)
INNER join subcategory s on(q.SubcategoryID=s.SubcategoryID) 
INNER join category c on (s.CategoryID=c.CategoryID) 
INNER JOIN survey on(c.SurveyID=survey.SurveyID)
WHERE a.AnswerId='".$value['ansId']."'
GROUP BY s.SubCategoryCode
UNION 
SELECT a1.AnswerID,a1.surveyID,a1.clientScore,c.CategoryCode,s.SubCategoryCode,count(q.QuestionID) as quesTot,sum(ad.Answer) as answer
FROM `answer` as a
LEFT JOIN answer as a1 on(a.userID=a1.UserID AND a1.SurveyStatus='1' AND a.Sequence=a1.Sequence AND a.surveyID<>a1.SurveyID AND a1.IsActive='1' )
INNER JOIN answerdetail ad on(a1.AnswerId=ad.AnswerID AND ad.IsActive='1')
INNER JOIN question q on(ad.QuestionID=q.QuestionID)
INNER join subcategory s on(q.SubcategoryID=s.SubcategoryID) 
INNER join category c on (s.CategoryID=c.CategoryID) 
INNER JOIN survey on(c.SurveyID=survey.SurveyID)
WHERE a.AnswerId='".$value['ansId']."'
GROUP BY s.SubCategoryCode";
			$queryIn =$this->db->query($sqlIn,NULL);
			$lr=$ot=array();
			$lrQuesTot=$otQuesTot=$lrAnsTot=$otAnsTot=$lrPassState=$otPassState=$lrPassNational=$otPassNational=0;
			foreach ($queryIn->result_array() as $keyIn => $valueIn) {
				if(trim($valueIn['surveyID'])=='1'){
					if(trim($valueIn['SubCategoryCode'])=='Standard B3'){
						$lr['b3']=round(($valueIn['answer']/($valueIn['quesTot']*2))*100,0);
					}
					if(trim($valueIn['SubCategoryCode'])=='Standard E18'){
						$lr['e18']=round(($valueIn['answer']/($valueIn['quesTot']*2))*100,0);
					}
					if(trim($valueIn['SubCategoryCode'])=='Standard E19'){
						$lr['e19']=round(($valueIn['answer']/($valueIn['quesTot']*2))*100,0);
					}
					if(!isset($lr['clientScore'])){
						if($valueIn['clientScore']==''){
							$lr['clientScore']='NA';
						} else {
							$lr['clientScore']=$valueIn['clientScore'];							
						}
					}
					if(isset($lr['cat'][$valueIn['CategoryCode']]['quesTot'])){
						$lr['cat'][$valueIn['CategoryCode']]['quesTot']+=$valueIn['quesTot'];
					} else {
						$lr['cat'][$valueIn['CategoryCode']]['quesTot']=$valueIn['quesTot'];
					}
					if(isset($lr['cat'][$valueIn['CategoryCode']]['answer'])){
						$lr['cat'][$valueIn['CategoryCode']]['answer']+=$valueIn['answer'];
					} else {
						$lr['cat'][$valueIn['CategoryCode']]['answer']=$valueIn['answer'];
					}					
					$lrQuesTot+=$valueIn['quesTot'];
					$lrAnsTot+=$valueIn['answer'];
				} else {
					if(trim($valueIn['SubCategoryCode'])=='Standard B3'){
						$ot['b3']=round(($valueIn['answer']/($valueIn['quesTot']*2))*100,0);
					}
					if(trim($valueIn['SubCategoryCode'])=='Standard E18'){
						$ot['e18']=round(($valueIn['answer']/($valueIn['quesTot']*2))*100,0);
					}
					if(trim($valueIn['SubCategoryCode'])=='Standard E19'){
						$ot['e19']=round(($valueIn['answer']/($valueIn['quesTot']*2))*100,0);
					}
					if(!isset($ot['clientScore'])){
						if($valueIn['clientScore']==''){
							$ot['clientScore']='NA';
						} else {
							$ot['clientScore']=$valueIn['clientScore'];							
						}
					}
					if(isset($ot['cat'][$valueIn['CategoryCode']]['quesTot'])){
						$ot['cat'][$valueIn['CategoryCode']]['quesTot']+=$valueIn['quesTot'];
					} else {
						$ot['cat'][$valueIn['CategoryCode']]['quesTot']=$valueIn['quesTot'];
					}
					if(isset($ot['cat'][$valueIn['CategoryCode']]['answer'])){
						$ot['cat'][$valueIn['CategoryCode']]['answer']+=$valueIn['answer'];
					} else {
						$ot['cat'][$valueIn['CategoryCode']]['answer']=$valueIn['answer'];
					}
					$otQuesTot+=$valueIn['quesTot'];
					$otAnsTot+=$valueIn['answer'];
				}
			}
			// query for cat4 start
			$sqlIn="(SELECT a.answerId,ad.Answer,a.surveyID,c.CategoryCode,s.SubCategoryCode,q.QuestionID,q.ParentID,q.Reference,q.Statement,q.Checkpoint
FROM `answer` as a INNER JOIN answerdetail ad on(a.AnswerId=ad.AnswerID AND ad.IsActive='1') INNER JOIN question q on(ad.QuestionID=q.QuestionID) INNER join subcategory s on(q.SubcategoryID=s.SubcategoryID) INNER join category c on (s.CategoryID=c.CategoryID) INNER JOIN survey on(c.SurveyID=survey.SurveyID) WHERE a.AnswerId='".$value['ansId']."' AND c.CategoryCode='G' ORDER by c.CategoryID ASC,s.SubcategoryID ASC,q.QuestionID,q.ParentID ASC,q.Serial ASC)  
UNION 
(SELECT a1.AnswerID,ad.Answer,a1.surveyID,c.CategoryCode,s.SubCategoryCode,q.QuestionID,q.ParentID,q.Reference,q.Statement,q.Checkpoint 
FROM `answer` as a LEFT JOIN answer as a1 on(a.userID=a1.UserID AND a1.SurveyStatus='1' AND a.Sequence=a1.Sequence AND a.surveyID<>a1.SurveyID AND a1.IsActive='1' ) INNER JOIN answerdetail ad on(a1.AnswerId=ad.AnswerID AND ad.IsActive='1') INNER JOIN question q on(ad.QuestionID=q.QuestionID) INNER join subcategory s on(q.SubcategoryID=s.SubcategoryID) INNER join category c on (s.CategoryID=c.CategoryID) INNER JOIN survey on(c.SurveyID=survey.SurveyID) WHERE a.AnswerId='".$value['ansId']."' AND c.CategoryCode='G' ORDER by c.CategoryID ASC,s.SubcategoryID ASC,q.QuestionID,q.ParentID ASC,q.Serial ASC)";
			$queryIn =$this->db->query($sqlIn,NULL);			
			foreach ($queryIn->result_array() as $keyIn => $valueIn) {				
				if(trim($valueIn['surveyID'])=='1'){
					if($valueIn['ParentID']=='0'){
						$ref=$valueIn['QuestionID'];
						$lr['subcat'][$ref]['Reference']=$valueIn['Reference'];
					} else {
						$ref=$valueIn['ParentID'];
					}
					if(isset($lr['subcat'][$ref]['Answer'])){
						$lr['subcat'][$ref]['Answer']+=$valueIn['Answer'];
					} else {
						$lr['subcat'][$ref]['Answer']=$valueIn['Answer'];
					}
					if(isset($lr['subcat'][$ref]['quesTot'])){
						$lr['subcat'][$ref]['quesTot']+=1;
					} else {
						$lr['subcat'][$ref]['quesTot']=1;
					}
				} else {
					if($valueIn['ParentID']=='0'){
						$ref=$valueIn['QuestionID'];
						$ot['subcat'][$ref]['Reference']=$valueIn['Reference'];
					} else {
						$ref=$valueIn['ParentID'];
					}
					if(isset($ot['subcat'][$ref]['Answer'])){
						$ot['subcat'][$ref]['Answer']+=$valueIn['Answer'];
					} else {
						$ot['subcat'][$ref]['Answer']=$valueIn['Answer'];
					}
					if(isset($ot['subcat'][$ref]['quesTot'])){
						$ot['subcat'][$ref]['quesTot']+=1;
					} else {
						$ot['subcat'][$ref]['quesTot']=1;
					}
				}
			}
			// query for cat4 end			

			// certificatin status Query for looped in facility
		  	$this->db->select('status,certification_type,level,certification_date,CertificationID,certificationStatus');
		    $this->db->from('certification');
		    $this->db->where('IsCurrent','1');
		    $this->db->where('IsActive','1');
		    $this->db->where('userID',$value['UserID']);
		    $this->db->where_in('level',array('1','2'));
		    $queryCertification = $this->db->get();
		    //echo $this->db->last_query();

			$certificationStatus=array();
			if($queryCertification->num_rows()>0){
				foreach ($queryCertification->result_array() as $keyCertification => $valueCertification) {
					$certificationStatus[$valueCertification['certification_type']][$valueCertification['level']]['status']=$valueCertification['status'];
					$certificationStatus[$valueCertification['certification_type']][$valueCertification['level']]['certification_date']=$valueCertification['certification_date'];
					$certificationStatus[$valueCertification['certification_type']][$valueCertification['level']]['CertificationID']=$valueCertification['CertificationID'];
					$certificationStatus[$valueCertification['certification_type']][$valueCertification['level']]['certificationStatus']=$valueCertification['certificationStatus'];
				}
			} else {
				
			}
			// certificatin status Query end
			
            $subdata = array();
            $subdata1 = array();
            $subdataNational = array();
            $subdata1National = array();
            $checkListUrl='reports/checklist';
            $lrStateCertified=$lrStateEligible=$lrNationalEligible=0;
            $otStateCertified=$otStateEligible=$otNationalEligible=0;
            if(empty($lr)){
            	// no lr data can't apply for lr state & national

            	//$subdata[] = $cnt_data.'.'.$cnt_data_in++;
				if($searchData['RoleName']=='Ministry'){
	            	$subdata[] = $value['StateName'];
	        	}
	        	if($searchData['RoleName']=='State' || $searchData['RoleName']=='Ministry'){
	            	$subdata[] = $value['DistrictName'];
	            }
	            if($searchData['RoleName']=='District' || $searchData['RoleName']=='State' || $searchData['RoleName']=='Ministry' ){
	            	$subdata[] = $value['FacilityName'];
	            }
	            $subdata[] = 'State Certification';
	            $subdata[] = 'Labour Room';
	            $subdata[] = 'Not Eligible';
	            $actionLink='';
	            if($access_eligibility){
	            	$actionLink.='<button data-href="'.base_url().$checkListUrl.'/'.$facilityUrl.'/state"  onclick="pageRedirect(this)" class="btn btn-primary btn-xs getScore   view-eligility" ><i class="fa fa-bar-chart"></i> View Eligibility Criteria</button>';
	            }
	            $subdata[] = $actionLink;

	            //$subdataNational[] = $cnt_data.'.'.$cnt_data_in++;
				if($searchData['RoleName']=='Ministry'){
	            	$subdataNational[] = $value['StateName'];
	        	}
	        	if($searchData['RoleName']=='State' || $searchData['RoleName']=='Ministry'){
	            	$subdataNational[] = $value['DistrictName'];
	            }
	            if($searchData['RoleName']=='District' || $searchData['RoleName']=='State' || $searchData['RoleName']=='Ministry' ){
	            	$subdataNational[] = $value['FacilityName'];
	            }
	            $subdataNational[] = 'National Certification';
	            $subdataNational[] = 'Labour Room';
	            $subdataNational[] = 'Not Eligible';
	            $actionLink='';
	            if($access_eligibility){
	            	$actionLink.='<button data-href="'.base_url().$checkListUrl.'/'.$facilityUrl.'/national"  onclick="pageRedirect(this)" class="btn btn-primary btn-xs getScore   view-eligility" ><i class="fa fa-bar-chart"></i> View Eligibility Criteria</button>';
	            }
	            $subdataNational[] = $actionLink;

            } else {
            	// check for number of criteria passed for lr in state and national started
				$lrScore=(int)(($lrAnsTot/($lrQuesTot*2))*100);
				$lrb3=isset($lr['b3'])?$lr['b3']:0;
				$lre18=isset($lr['e18'])?$lr['e18']:0;
				$lre19=isset($lr['e19'])?$lr['e19']:0;
				$lrclientScore=isset($lr['clientScore'])?$lr['clientScore']:0;
				if($lrScore>=65){
					$lrPassState++;
				}
				if($lrScore>=70){
					$lrPassNational++;
				}

				if(empty($lr['cat'])){
					
				} else {
					$lrCrtState='1';
					$lrCrtNational='1';
					foreach ($lr['cat'] as $keyCat => $valueCat) {
						$percent=(int)(($valueCat['answer']*100)/($valueCat['quesTot']*2));
						if($percent>=65){							
						} else {
							$lrCrtState='0';							
						}
						if($percent>=70){
						} else {
							$lrCrtNational='0';
						}
					}					
					if($lrCrtState=='1'){
						$lrPassState++;
					}					
					if($lrCrtNational=='1'){
						$lrPassNational++;
					}
				}
				if($lrb3>=65 && $lre18>=65 && $lre19>=65){
					$lrPassState++;
				}
				if($lrb3>=70 && $lre18>=70 && $lre19>=70){
					$lrPassNational++;
				}
				if(empty($lr['subcat'])){
					
				} else {
					$lrCrtState='1';$lrCrtNational='1';
					foreach ($lr['subcat'] as $keysubcat => $valuesubcat) {
						$percent=(int)(($valuesubcat['Answer']*100)/($valuesubcat['quesTot']*2));
						if($percent>45){
						} else {
							$lrCrtState='0';
						}
						if($percent>50){
						} else {
							$lrCrtNational='0';
						}
					}					
					if($lrCrtState=='1'){
						$lrPassState++;
					}					
					if($lrCrtNational=='1'){
						$lrPassNational++;
					}
				}
				if($lrclientScore>=65){
					$lrPassState++;
				}
				if($lrclientScore>=70){
					$lrPassNational++;
				}
				// check for number of criteria passed for lr in state and national ended
            }
            if(empty($ot)){
            	// no ot data, can't apply for state and national certification 

            	//$subdata1[] = $cnt_data.'.'.$cnt_data_in++;
				if($searchData['RoleName']=='Ministry'){
	            	$subdata1[] = $value['StateName'];
	        	}
	        	if($searchData['RoleName']=='State' || $searchData['RoleName']=='Ministry'){
	            	$subdata1[] = $value['DistrictName'];
	            }
	            if($searchData['RoleName']=='District' || $searchData['RoleName']=='State' || $searchData['RoleName']=='Ministry' ){
	            	$subdata1[] = $value['FacilityName'];
	            }
	            $subdata1[] = 'State Certification';
	            $subdata1[] = 'Operation Theater';
	            $subdata1[] = 'Not Eligible';
	            $actionLink='';
	            if($access_eligibility){
	            	$actionLink.='<button data-href="'.base_url().$checkListUrl.'/'.$facilityUrl.'/state"  onclick="pageRedirect(this)" class="btn btn-primary btn-xs getScore   view-eligility" ><i class="fa fa-bar-chart"></i> View Eligibility Criteria</button>';
	            }
	            $subdata1[] = $actionLink;

				//$subdata1National[] = $cnt_data.'.'.$cnt_data_in++;
				if($searchData['RoleName']=='Ministry'){
	            	$subdata1National[] = $value['StateName'];
	        	}
	        	if($searchData['RoleName']=='State' || $searchData['RoleName']=='Ministry'){
	            	$subdata1National[] = $value['DistrictName'];
	            }
	            if($searchData['RoleName']=='District' || $searchData['RoleName']=='State' || $searchData['RoleName']=='Ministry' ){
	            	$subdata1National[] = $value['FacilityName'];
	            }
	            $subdata1National[] = 'National Certification';
	            $subdata1National[] = 'Operation Theater';
	            $subdata1National[] = 'Not Eligible';
	            $actionLink='';
	            if($access_eligibility){
	            	$actionLink.='<button data-href="'.base_url().$checkListUrl.'/'.$facilityUrl.'/national"  onclick="pageRedirect(this)" class="btn btn-primary btn-xs getScore   view-eligility" ><i class="fa fa-bar-chart"></i> View Eligibility Criteria</button>';
	            }
	            $subdata1National[] = $actionLink;

            } else {
            	// check for number of criteria passed for ot in state and national started
            	$otScore=(int)(($otAnsTot/($otQuesTot*2))*100);
				$otb3=isset($ot['b3'])?$ot['b3']:0;
				$ote18=isset($ot['e18'])?$ot['e18']:0;
				$ote19=isset($ot['e19'])?$ot['e19']:0;
				$otclientScore=isset($ot['clientScore'])?$ot['clientScore']:0;
				if($otScore>=65){
					$otPassState++;
				}
				if($otScore>=70){
					$otPassNational++;
				}
				if(empty($ot['cat'])){					
				} else {
					$otCrtState='1';$otCrtNational='1';
					foreach ($ot['cat'] as $keyCat => $valueCat) {
						$percent=(int)(($valueCat['answer']*100)/($valueCat['quesTot']*2));
						if($percent>=65){
						} else {
							$otCrtState='0';
						}
						if($percent>=70){
						} else {
							$otCrtNational='0';
						}
					}
					if($otCrtState=='1'){
						$otPassState++;
					}
					if($otCrtNational=='1'){
						$otPassNational++;
					}
				}
				if($otb3>=65 && $ote18>=65 && $ote19>=65){
					$otPassState++;
				}
				if($otb3>=70 && $ote18>=70 && $ote19>=70){
					$otPassNational++;
				}
				if(empty($ot['subcat'])){
				} else {
					$otCrtState='1';$otCrtNational='1';
					foreach ($ot['subcat'] as $keysubcat => $valuesubcat) {
						$percent=(int)(($valuesubcat['Answer']*100)/($valuesubcat['quesTot']*2));
						if($percent>45){
						} else {
							$otCrtState='0';
						}
						if($percent>50){
						} else {
							$otCrtNational='0';
						}
					}
					if($otCrtState=='1'){
						$otPassState++;
					}
					if($otCrtNational=='1'){
						$otPassNational++;
					}
				}
				if($otclientScore>=65){
					$otPassState++;
				}
				if($otclientScore>=70){
					$otPassNational++;
				}
				// check for number of criteria passed for ot in state and national ended
            }
            if($lrPassState==5){ $lrStateEligible=1; }
            if($lrPassNational==5){ $lrNationalEligible=1; }
            if($otPassState==5){ $otStateEligible=1; }
            if($otPassNational==5){ $otNationalEligible=1; }
            $bothStateSts=0;
            // remove both status as lr and ot certification will seperatly get
            /*if($lrStateEligible && $otStateEligible){
				$this->db->select('CertificationID,certification_type,status,');
				$this->db->from('certification');
				$this->db->where('userID',$value['UserID']);		    
		    	$this->db->where('IsActive','1');
		    	$this->db->where_in('level',array('2'));
		    	$this->db->order_by('CertificationID','DESC');
				$queryBothCheck=$this->db->get();
				$queryBothCheckCount=$queryBothCheck->num_rows();
				if($queryBothCheckCount==0){
					// no State certification presently applied yet
					$bothStateSts=1;
				} else {
					// State certification presently applied for both certification only
					$queryBothCheckData=$queryBothCheck->result_array();
					if($queryBothCheckData[0]['certification_type']=='both'){
						$bothStateSts=1;
					}
				}
            	
            }*/

            
            if($bothStateSts){
            	// state both lr&ot started
            	$CertificationSts=(empty($certificationStatus['both'][2]['status']) || $certificationStatus['both'][2]['certificationStatus']=='0')?'Eligible - Not Applied':$this->config->item('certificationStatusEligible')[$certificationStatus['both'][2]['status']];
            	//$subdata[] = $cnt_data.'.'.$cnt_data_in++;
				if($searchData['RoleName']=='Ministry'){
	            	$subdata[] = $value['StateName'];
	        	}
	        	if($searchData['RoleName']=='State' || $searchData['RoleName']=='Ministry'){
	            	$subdata[] = $value['DistrictName'];
	            }
	            if($searchData['RoleName']=='District' || $searchData['RoleName']=='State' || $searchData['RoleName']=='Ministry' ){
	            	$subdata[] = $value['FacilityName'];
	            }
	            $subdata[] = 'State Certification';
	            $subdata[] = 'Labour Room & Operation Theater';
	            $subdata[] = $CertificationSts;

	            $renewed=0;
	            $actionLink='';
				if(!empty($certificationStatus['both'][2]['certification_date']) && date('Y',strtotime($certificationStatus['both'][2]['certification_date']))>2000){
					// check for already certified and 1 year crossed
					$start_date = new DateTime($certificationStatus['both'][2]['certification_date']);
					$since_start = $start_date->diff(new DateTime(date('Y-m-d')));
					$timeElapsed=$since_start->format("%y"); // in year
					// /$timeElapsed=$since_start->format("%a");  // in day
					if($timeElapsed>=1 && $certificationStatus['both'][2]['status']=='3'){
						$renewed=1;
					}						
				}
	            if(empty($certificationStatus['both'][2]['status']) || $renewed==1 ){
	            	if($renewed=='1'){
	            		$actionTxt='Renewal';
	            	} else {
	            		$actionTxt='Apply';
	            	}
					if($access_add){
					    $actionLink.='<button data-href="'.base_url().'certification/apply/'.encryptor($value['UserID']).'/'.encryptor('State').'/'.encryptor('both').'"  onclick="pageRedirect(this)" class="btn btn-primary btn-xs view apply-eligility" ><i class="fa fa-certificate" aria-hidden="true"></i> '.$actionTxt.'</button>';
					}
	            	$subdata[] = $actionLink;
	            } else if(in_array($certificationStatus['both'][2]['status'], array('5','1'))){
	            	if($access_view){
	            		$actionLink.='<button data-href="'.base_url().'certification/certificationview/'.encryptor($certificationStatus['both'][2]['CertificationID']).'"  onclick="pageRedirect(this)" class="btn btn-primary btn-xs view" ><i class="glyphicon glyphicon-check" aria-hidden="true"></i> View</button>';
	            	}
	            	if($certificationStatus['both'][2]['certificationStatus']=='1'){
	            		if($access_approval){
	            			$actionLink.='<button data-href="'.base_url().'certification/approvaledit/'.encryptor($certificationStatus['both'][2]['CertificationID']).'"  onclick="pageRedirect(this)" class="btn btn-primary btn-xs view" ><i class="fa fa-check-square" aria-hidden="true"></i> Submission Approval</button>';
	            		}
	            	} else {
	            		if($access_edit){
	            			$actionLink.='<button data-href="'.base_url().'certification/apply/'.encryptor($value['UserID']).'/'.encryptor('State').'/'.encryptor('both').'/'.encryptor($certificationStatus['both'][2]['CertificationID']).'"  onclick="pageRedirect(this)" class="btn btn-primary btn-xs view" ><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button>';
	            		}
	            	}
	            	$subdata[] = $actionLink;
	            } else {
	            	if($access_view){
	            		$actionLink.= '<button data-href="'.base_url().'certification/certificationview/'.encryptor($certificationStatus['both'][2]['CertificationID']).'"  onclick="pageRedirect(this)" class="btn btn-primary btn-xs view" ><i class="glyphicon glyphicon-check" aria-hidden="true"></i> View</button>';
	            	}
	            	$subdata[] = $actionLink;
	            }
            } else {
				if($lrStateEligible){
					$CertificationSts=(empty($certificationStatus['lr'][2]['status']) || $certificationStatus['lr'][2]['certificationStatus']=='0')?'Eligible - Not Applied':$this->config->item('certificationStatusEligible')[$certificationStatus['lr'][2]['status']];
					//$subdata[] = $cnt_data.'.'.$cnt_data_in++;
					if($searchData['RoleName']=='Ministry'){
		            	$subdata[] = $value['StateName'];
		        	}
		        	if($searchData['RoleName']=='State' || $searchData['RoleName']=='Ministry'){
		            	$subdata[] = $value['DistrictName'];
		            }
		            if($searchData['RoleName']=='District' || $searchData['RoleName']=='State' || $searchData['RoleName']=='Ministry' ){
		            	$subdata[] = $value['FacilityName'];
		            }
		            $subdata[] = 'State Certification';
		            $subdata[] = 'Labour Room';
		            $subdata[] = $CertificationSts;

		            $renewed=0;
		            $actionLink='';
					if(!empty($certificationStatus['lr'][2]['certification_date']) && date('Y',strtotime($certificationStatus['lr'][2]['certification_date']))>2000){
						$start_date = new DateTime($certificationStatus['lr'][2]['certification_date']);
						$since_start = $start_date->diff(new DateTime(date('Y-m-d')));
						$timeElapsed=$since_start->format("%y"); // in year
						//$timeElapsed=$since_start->format("%a");  // in day
						if($timeElapsed>=1 && $certificationStatus['lr'][2]['status']=='3'){
							$renewed=1;
						}						
					}
		            if(empty($certificationStatus['lr'][2]['status']) || $renewed==1 ){
	                    if($renewed=='1'){
	                        $actionTxt='Renewal';
	                    } else {
	                        $actionTxt='Apply';
	                    }
						if($access_add){
						    $actionLink.='<button data-href="'.base_url().'certification/apply/'.encryptor($value['UserID']).'/'.encryptor('State').'/'.encryptor('lr').'"  onclick="pageRedirect(this)" class="btn btn-primary btn-xs view" ><i class="fa fa-certificate" aria-hidden="true"></i> '.$actionTxt.'</button>';
						}	                    
		            	$subdata[] = $actionLink;
		            } else if(in_array($certificationStatus['lr'][2]['status'], array('5','1'))){
		            	if($access_view){
		            		$actionLink.= '<button data-href="'.base_url().'certification/certificationview/'.encryptor($certificationStatus['lr'][2]['CertificationID']).'"  onclick="pageRedirect(this)" class="btn btn-primary btn-xs view" ><i class="glyphicon glyphicon-check" aria-hidden="true"></i> View</button>';
		            	}
		            	if($certificationStatus['lr'][2]['certificationStatus']=='1' && $certificationStatus['lr'][2]['status']=='1'){
		            		if($access_approval){
		            			$actionLink.='<button data-href="'.base_url().'certification/approvaledit/'.encryptor($certificationStatus['lr'][2]['CertificationID']).'"  onclick="pageRedirect(this)" class="btn btn-primary btn-xs view" ><i class="fa fa-check-square" aria-hidden="true"></i> Submission Approval</button>';
		            		}
		            	} else if($certificationStatus['lr'][2]['certificationStatus']=='0' && $certificationStatus['lr'][2]['status']=='1'){
		            		if($access_edit){
		            			$actionLink.='<button data-href="'.base_url().'certification/apply/'.encryptor($value['UserID']).'/'.encryptor('State').'/'.encryptor('lr').'/'.encryptor($certificationStatus['lr'][2]['CertificationID']).'"  onclick="pageRedirect(this)" class="btn btn-primary btn-xs view" ><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button>';
		            		}
		            	}
	            		if($access_edit && $certificationStatus['lr'][2]['status']=='5'){
	            			$actionLink.='<button data-href="'.base_url().'certification/apply/'.encryptor($value['UserID']).'/'.encryptor('State').'/'.encryptor('lr').'/'.encryptor($certificationStatus['lr'][2]['CertificationID']).'"  onclick="pageRedirect(this)" class="btn btn-primary btn-xs view" ><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button>';
	            		}
		            	$subdata[] = $actionLink;
		            } else {
		            	if($access_view){
		            		$actionLink.='<button data-href="'.base_url().'certification/certificationview/'.encryptor($certificationStatus['lr'][2]['CertificationID']).'"  onclick="pageRedirect(this)" class="btn btn-primary btn-xs view" ><i class="glyphicon glyphicon-check" aria-hidden="true"></i> View</button>';
		            	}
		            	$subdata[] = $actionLink;
		            }

		            
				} else {
					// not elegible lr state and national certification as state certification criteria not meet
					//$subdata[] = $cnt_data.'.'.$cnt_data_in++;
					if($searchData['RoleName']=='Ministry'){
		            	$subdata[] = $value['StateName'];
		        	}
		        	if($searchData['RoleName']=='State' || $searchData['RoleName']=='Ministry'){
		            	$subdata[] = $value['DistrictName'];
		            }
		            if($searchData['RoleName']=='District' || $searchData['RoleName']=='State' || $searchData['RoleName']=='Ministry' ){
		            	$subdata[] = $value['FacilityName'];
		            }
		            $subdata[] = 'State Certification';
		            $subdata[] = 'Labour Room';
		            $subdata[] = 'Not Eligible';
		            $actionLink='';
		            if($access_eligibility){
		            	$actionLink.='<button data-href="'.base_url().$checkListUrl.'/'.$facilityUrl.'/state"  onclick="pageRedirect(this)" class="btn btn-primary btn-xs getScore   view-eligility" ><i class="fa fa-bar-chart"></i> View Eligibility Criteria</button>';
		            }
		            $subdata[] = $actionLink;
				} 

				if($otStateEligible){
					$CertificationSts=(empty($certificationStatus['ot'][2]['status']) || $certificationStatus['ot'][2]['certificationStatus']=='0')?'Eligible - Not Applied':$this->config->item('certificationStatus')[$certificationStatus['ot'][2]['status']];
					//$subdata1[] = $cnt_data.'.'.$cnt_data_in++;
					if($searchData['RoleName']=='Ministry'){
		            	$subdata1[] = $value['StateName'];
		        	}
		        	if($searchData['RoleName']=='State' || $searchData['RoleName']=='Ministry'){
		            	$subdata1[] = $value['DistrictName'];
		            }
		            if($searchData['RoleName']=='District' || $searchData['RoleName']=='State' || $searchData['RoleName']=='Ministry' ){
		            	$subdata1[] = $value['FacilityName'];
		            }
		            $subdata1[] = 'State Certification';
		            $subdata1[] = 'Operation Theater';
		            $subdata1[] = $CertificationSts;

		            $renewed=0;
		            $actionLink='';
					if(!empty($certificationStatus['ot'][2]['certification_date']) && date('Y',strtotime($certificationStatus['ot'][2]['certification_date']))>2000){
						$start_date = new DateTime($certificationStatus['lr'][2]['certification_date']);
						$since_start = $start_date->diff(new DateTime(date('Y-m-d')));
						$timeElapsed=$since_start->format("%y"); // in year
						//$timeElapsed=$since_start->format("%a");  // in day
						if($timeElapsed>=1 && $certificationStatus['ot'][2]['status']=='3'){
							$renewed=1;
						}						
					}
		            if(empty($certificationStatus['ot'][2]['status']) || $renewed==1 ){
	                    if($renewed=='1'){
	                        $actionTxt='Renewal';
	                    } else {
	                        $actionTxt='Apply';
	                    }
	                    if($access_add){
	                    	$actionLink.='<button data-href="'.base_url().'certification/apply/'.encryptor($value['UserID']).'/'.encryptor('State').'/'.encryptor('ot').'"  onclick="pageRedirect(this)" class="btn btn-primary btn-xs view" ><i class="fa fa-certificate" aria-hidden="true"></i> '.$actionTxt.'</button>';
	                    }
		            	$subdata1[] = $actionLink;
		            } else if(in_array($certificationStatus['ot'][2]['status'], array('5','1'))){
		            	if($access_view){
		            		$actionLink.= '<button data-href="'.base_url().'certification/certificationview/'.encryptor($certificationStatus['ot'][2]['CertificationID']).'"  onclick="pageRedirect(this)" class="btn btn-primary btn-xs view" ><i class="glyphicon glyphicon-check" aria-hidden="true"></i> View</button>';
		            	}
		            	if($certificationStatus['ot'][2]['certificationStatus']=='1' && $certificationStatus['ot'][2]['status']=='1'){
		            		if($access_approval){
		            			$actionLink.='<button data-href="'.base_url().'certification/approvaledit/'.encryptor($certificationStatus['ot'][2]['CertificationID']).'"  onclick="pageRedirect(this)" class="btn btn-primary btn-xs view" ><i class="fa fa-check-square" aria-hidden="true"></i> Submission Approval</button>';
		            		}
		            	} else if($certificationStatus['ot'][2]['certificationStatus']=='0' && $certificationStatus['ot'][2]['status']=='1'){
		            		if($access_edit){
		            			$actionLink.='<button data-href="'.base_url().'certification/apply/'.encryptor($value['UserID']).'/'.encryptor('State').'/'.encryptor('ot').'/'.encryptor($certificationStatus['ot'][2]['CertificationID']).'"  onclick="pageRedirect(this)" class="btn btn-primary btn-xs view" ><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button>';
		            		}
		            	}
	            		if($access_edit && $certificationStatus['ot'][2]['status']=='5'){
	            			$actionLink.='<button data-href="'.base_url().'certification/apply/'.encryptor($value['UserID']).'/'.encryptor('State').'/'.encryptor('ot').'/'.encryptor($certificationStatus['ot'][2]['CertificationID']).'"  onclick="pageRedirect(this)" class="btn btn-primary btn-xs view" ><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button>';
	            		}
		            	$subdata1[] = $actionLink;
		            } else {
		            	if($access_view){
		            		$actionLink.='<button data-href="'.base_url().'certification/certificationview/'.encryptor($certificationStatus['ot'][2]['CertificationID']).'"  onclick="pageRedirect(this)" class="btn btn-primary btn-xs view" ><i class="glyphicon glyphicon-check" aria-hidden="true"></i> View</button>';
		            	}
		            	$subdata1[] = $actionLink;
		            }
				} else {
					// not elegible ot state and national certification as state certification criteria not meet
					//$subdata1[] = $cnt_data.'.'.$cnt_data_in++;
					if($searchData['RoleName']=='Ministry'){
		            	$subdata1[] = $value['StateName'];
		        	}
		        	if($searchData['RoleName']=='State' || $searchData['RoleName']=='Ministry'){
		            	$subdata1[] = $value['DistrictName'];
		            }
		            if($searchData['RoleName']=='District' || $searchData['RoleName']=='State' || $searchData['RoleName']=='Ministry' ){
		            	$subdata1[] = $value['FacilityName'];
		            }
		            $subdata1[] = 'State Certification';
		            $subdata1[] = 'Operation Theater';
		            $subdata1[] = 'Not Eligible';
		            $actionLink='';
		            if($access_eligibility){
		            	$actionLink.='<button data-href="'.base_url().$checkListUrl.'/'.$facilityUrl.'/state"  onclick="pageRedirect(this)" class="btn btn-primary btn-xs getScore   view-eligility" ><i class="fa fa-bar-chart"></i> View Eligibility Criteria</button>';
		            }
		            $subdata1[] = $actionLink;
				}
            }
            // state both lr&ot ended

            // National both lr&ot started
            $bothNationalSts=0;
			// check state certification cleared or not
			/*$this->db->select('CertificationID,certification_type');
			$this->db->from('certification');
			$this->db->where('userID',$value['UserID']);		    
	    	$this->db->where('IsActive','1');
	    	$this->db->where('IsCurrent','1');
	    	$this->db->where('status','3');
	    	$this->db->where_in('level',array('2'));
	    	$this->db->order_by('CertificationID','DESC');
			$queryBothCheck=$this->db->get();
			$checkStsState=$queryBothCheck->num_rows();*/

            // remove it as lr/ot certification will seperatly done
            /*if($lrNationalEligible && $otNationalEligible && $checkStsState>=2){
				$this->db->select('CertificationID,certification_type');
				$this->db->from('certification');
				$this->db->where('userID',$value['UserID']);		    
		    	$this->db->where('IsActive','1');
		    	$this->db->where_in('level',array('1'));
		    	$this->db->order_by('CertificationID','DESC');
				$queryBothCheck=$this->db->get();
				if($queryBothCheck->num_rows()==0){
					// no National certification presently applied yet
					$bothNationalSts=1;
				} else {
					// allow only if applied and applied for both certification
					$queryBothCheckData=$queryBothCheck->result_array();
					if($queryBothCheckData[0]['certification_type']=='both'){
						$bothNationalSts=1;
					}
				}            	
            }*/
            if($bothNationalSts){
				$CertificationSts=(empty($certificationStatus['both'][1]['status']) || $certificationStatus['both'][1]['certificationStatus']=='0')?'Eligible - Not Applied':$this->config->item('certificationStatus')[$certificationStatus['both'][1]['status']];
				//$subdataNational[] = $cnt_data.'.'.$cnt_data_in++;
				if($searchData['RoleName']=='Ministry'){
	            	$subdataNational[] = $value['StateName'];
	        	}
	        	if($searchData['RoleName']=='State' || $searchData['RoleName']=='Ministry'){
	            	$subdataNational[] = $value['DistrictName'];
	            }
	            if($searchData['RoleName']=='District' || $searchData['RoleName']=='State' || $searchData['RoleName']=='Ministry' ){
	            	$subdataNational[] = $value['FacilityName'];
	            }
	            $subdataNational[] = 'National Certification';
	            $subdataNational[] = 'Labour Room & Operation Theater';
	            $subdataNational[] = $CertificationSts;

	            $renewed=0;
	            $actionLink='';
				if(!empty($certificationStatus['both'][1]['certification_date']) && date('Y',strtotime($certificationStatus['both'][1]['certification_date']))>2000){
					$start_date = new DateTime($certificationStatus['both'][2]['certification_date']);
					$since_start = $start_date->diff(new DateTime(date('Y-m-d')));
					//$timeElapsed=$since_start->format("%y"); // in year
					$timeElapsed=$since_start->format("%a");  // in day
					if($timeElapsed>1 && $certificationStatus['both'][1]['status']=='3'){
						$renewed=1;
					}						
				}
	            if(empty($certificationStatus['both'][1]['status']) || $renewed==1 ){
                    if($renewed=='1'){
                        $actionTxt='Renewal';
                    } else {
                        $actionTxt='Apply';
                    }
                    if($access_add){
                    	$actionLink.='<button data-href="'.base_url().'certification/apply/'.encryptor($value['UserID']).'/'.encryptor('National').'/'.encryptor('both').'"  onclick="pageRedirect(this)" class="btn btn-primary btn-xs view apply-eligility" ><i class="fa fa-certificate" aria-hidden="true"></i> '.$actionTxt.'</button>';
                    }
	            	$subdataNational[] = $actionLink;
	            } else if(in_array($certificationStatus['both'][1]['status'], array('5','1'))){
	            	if($access_view){
	            		$actionLink.= '<button data-href="'.base_url().'certification/certificationview/'.encryptor($certificationStatus['both'][1]['CertificationID']).'"  onclick="pageRedirect(this)" class="btn btn-primary btn-xs view" ><i class="glyphicon glyphicon-check" aria-hidden="true"></i> View</button>';
	            	}
	            	if($certificationStatus['both'][1]['certificationStatus']=='1'){
	            		if($access_approval){
	            			$actionLink.='<button data-href="'.base_url().'certification/approvaledit/'.encryptor($certificationStatus['both'][1]['CertificationID']).'"  onclick="pageRedirect(this)" class="btn btn-primary btn-xs view" ><i class="fa fa-check-square" aria-hidden="true"></i> Submission Approval</button>';
	            		}
	            	} else {
	            		if($access_edit){
	            			$actionLink.='<button data-href="'.base_url().'certification/apply/'.encryptor($value['UserID']).'/'.encryptor('National').'/'.encryptor('both').'/'.encryptor($certificationStatus['both'][1]['CertificationID']).'"  onclick="pageRedirect(this)" class="btn btn-primary btn-xs view apply-eligility" ><i class="fa fa-pencil-square-o" aria-hidden="true"></i>Edit</button>';
	            		}
	            	}
	            	$subdataNational[] =$actionLink;
	            } else {
            		if($access_view){
            			$actionLink.='<button data-href="'.base_url().'certification/certificationview/'.encryptor($certificationStatus['both'][1]['CertificationID']).'"  onclick="pageRedirect(this)" class="btn btn-primary btn-xs view" ><i class="glyphicon glyphicon-check" aria-hidden="true"></i> View</button>';
            		}
	            	$subdataNational[] = $actionLink;
	            }
            } else {
				if($lrNationalEligible && !empty($certificationStatus['lr'][2]['status']) && in_array($certificationStatus['lr'][2]['status'], array('3','6','7'))){
					$CertificationSts=(empty($certificationStatus['lr'][1]['status']) || $certificationStatus['lr'][1]['certificationStatus']=='0')?'Eligible - Not Applied':$this->config->item('certificationStatus')[$certificationStatus['lr'][1]['status']];
					//$subdataNational[] = $cnt_data.'.'.$cnt_data_in++;
					if($searchData['RoleName']=='Ministry'){
		            	$subdataNational[] = $value['StateName'];
		        	}
		        	if($searchData['RoleName']=='State' || $searchData['RoleName']=='Ministry'){
		            	$subdataNational[] = $value['DistrictName'];
		            }
		            if($searchData['RoleName']=='District' || $searchData['RoleName']=='State' || $searchData['RoleName']=='Ministry' ){
		            	$subdataNational[] = $value['FacilityName'];
		            }
		            $subdataNational[] = 'National Certification';
		            $subdataNational[] = 'Labour Room';
		            $subdataNational[] = $CertificationSts;

		            $renewed=0;
		            $actionLink='';
					if(!empty($certificationStatus['lr'][1]['certification_date']) && date('Y',strtotime($certificationStatus['lr'][1]['certification_date']))>2000){
						$start_date = new DateTime($certificationStatus['lr'][1]['certification_date']);
						$since_start = $start_date->diff(new DateTime(date('Y-m-d')));
						//$timeElapsed=$since_start->format("%y"); // in year
						$timeElapsed=$since_start->format("%a");  // in day
						if($timeElapsed>1 && in_array($certificationStatus['lr'][1]['status'], array('3','6','7'))){
							$renewed=1;
						}						
					}
		            if(empty($certificationStatus['lr'][1]['status']) || $renewed==1 ){
	                    if($renewed=='1'){
	                        $actionTxt='Renewal';
	                    } else {
	                        $actionTxt='Apply';
	                    }
	                    if($access_add){
	                    	$actionLink.='<button data-href="'.base_url().'certification/apply/'.encryptor($value['UserID']).'/'.encryptor('National').'/'.encryptor('lr').'"  onclick="pageRedirect(this)" class="btn btn-primary btn-xs view apply-eligility" ><i class="fa fa-certificate" aria-hidden="true"></i> '.$actionTxt.'</button>';
	                    }
		            	$subdataNational[] = $actionLink;
		            } else if(in_array($certificationStatus['lr'][1]['status'], array('5','1'))){
		            	if($access_view){
		            		$actionLink.= '<button data-href="'.base_url().'certification/certificationview/'.encryptor($certificationStatus['lr'][1]['CertificationID']).'"  onclick="pageRedirect(this)" class="btn btn-primary btn-xs view" ><i class="glyphicon glyphicon-check" aria-hidden="true"></i> View</button>';
		            	}
		            	if($certificationStatus['lr'][1]['certificationStatus']=='1'){
		            		if($access_approval){
		            			$actionLink.= '<button data-href="'.base_url().'certification/approvaledit/'.encryptor($certificationStatus['lr'][1]['CertificationID']).'"  onclick="pageRedirect(this)" class="btn btn-primary btn-xs view" ><i class="fa fa-check-square" aria-hidden="true"></i> Submission Approval</button>';
		            		}
		            	} else {
		            		if($access_edit){
		            			$actionLink.= '<button data-href="'.base_url().'certification/apply/'.encryptor($value['UserID']).'/'.encryptor('National').'/'.encryptor('lr').'/'.encryptor($certificationStatus['lr'][1]['CertificationID']).'"  onclick="pageRedirect(this)" class="btn btn-primary btn-xs view" ><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button>';
		            		}
		            	}
		            	$subdataNational[] = $actionLink;

		            } else {
		            	if($access_view){
		            		$actionLink.='<button data-href="'.base_url().'certification/certificationview/'.encryptor($certificationStatus['lr'][1]['CertificationID']).'"  onclick="pageRedirect(this)" class="btn btn-primary btn-xs view" ><i class="glyphicon glyphicon-check" aria-hidden="true"></i> View</button>';
		            	}
		            	$subdataNational[] = $actionLink;
		            }
				} else {
					// not elegible for lr national certification 
					//$subdataNational[] = $cnt_data.'.'.$cnt_data_in++;
					if($searchData['RoleName']=='Ministry'){
		            	$subdataNational[] = $value['StateName'];
		        	}
		        	if($searchData['RoleName']=='State' || $searchData['RoleName']=='Ministry'){
		            	$subdataNational[] = $value['DistrictName'];
		            }
		            if($searchData['RoleName']=='District' || $searchData['RoleName']=='State' || $searchData['RoleName']=='Ministry' ){
		            	$subdataNational[] = $value['FacilityName'];
		            }
		            $subdataNational[] = 'National Certification';
		            $subdataNational[] = 'Labour Room';
		            $subdataNational[] = 'Not Eligible';
		            $actionLink='';
		            if($access_eligibility){
		            	$actionLink.='<button data-href="'.base_url().$checkListUrl.'/'.$facilityUrl.'/national"  onclick="pageRedirect(this)" class="btn btn-primary btn-xs getScore   view-eligility" ><i class="fa fa-bar-chart"></i> View Eligibility Criteria</button>';
		            }
		            $subdataNational[] = $actionLink;
				}

	            if($otNationalEligible && !empty($certificationStatus['ot'][2]['status']) && in_array($certificationStatus['ot'][2]['status'], array('3','6','7'))){
	            	$CertificationSts=(empty($certificationStatus['ot'][1]['status']) || $certificationStatus['ot'][1]['certificationStatus']=='0')?'Eligible - Not Applied':$this->config->item('certificationStatus')[$certificationStatus['ot'][1]['status']];
					//$subdata1National[] = $cnt_data.'.'.$cnt_data_in++;
					if($searchData['RoleName']=='Ministry'){
		            	$subdata1National[] = $value['StateName'];
		        	}
		        	if($searchData['RoleName']=='State' || $searchData['RoleName']=='Ministry'){
		            	$subdata1National[] = $value['DistrictName'];
		            }
		            if($searchData['RoleName']=='District' || $searchData['RoleName']=='State' || $searchData['RoleName']=='Ministry' ){
		            	$subdata1National[] = $value['FacilityName'];
		            }
		            $subdata1National[] = 'National Certification';
		            $subdata1National[] = 'Operation Theater';
		            $subdata1National[] = $CertificationSts;

		            $renewed=0;
		            $actionLink='';
					if(!empty($certificationStatus['ot'][1]['certification_date']) && date('Y',strtotime($certificationStatus['ot'][1]['certification_date']))>2000){
						$start_date = new DateTime($certificationStatus['ot'][1]['certification_date']);
						$since_start = $start_date->diff(new DateTime(date('Y-m-d')));
						$timeElapsed=$since_start->format("%y"); // in year
						//$timeElapsed=$since_start->format("%a");  // in day
						if($timeElapsed>=3 && in_array($certificationStatus['ot'][1]['status'], array('3','6','7'))){
							$renewed=1;
						}						
					}
		            if(empty($certificationStatus['ot'][1]['status']) || $renewed==1 ){
	                    if($renewed=='1'){
	                        $actionTxt='Renewal';
	                    } else {
	                        $actionTxt='Apply';
	                    }
	                    if($access_add){
	                    	$actionLink.='<button data-href="'.base_url().'certification/apply/'.encryptor($value['UserID']).'/'.encryptor('National').'/'.encryptor('ot').'"  onclick="pageRedirect(this)" class="btn btn-primary btn-xs view" ><i class="fa fa-certificate" aria-hidden="true"></i> '.$actionTxt.'</button>';
	                    }
		            	$subdata1National[] = $actionLink;
		            } else if(in_array($certificationStatus['ot'][1]['status'], array('5','1'))){
		            	if($access_view){
		            		$actionLink.= '<button data-href="'.base_url().'certification/certificationview/'.encryptor($certificationStatus['lr'][1]['CertificationID']).'"  onclick="pageRedirect(this)" class="btn btn-primary btn-xs view" ><i class="glyphicon glyphicon-check" aria-hidden="true"></i> View</button>';
		            	}
		            	if($certificationStatus['ot'][1]['certificationStatus']=='1'){
		            		if($access_approval){
		            			$actionLink.='<button data-href="'.base_url().'certification/approvaledit/'.encryptor($certificationStatus['ot'][1]['CertificationID']).'"  onclick="pageRedirect(this)" class="btn btn-primary btn-xs view" ><i class="fa fa-check-square" aria-hidden="true"></i> Submission Approval</button>';
		            		}
		            	} else {
		            		if($access_edit){
		            			$actionLink.='<button data-href="'.base_url().'certification/apply/'.encryptor($value['UserID']).'/'.encryptor('National').'/'.encryptor('ot').'/'.encryptor($certificationStatus['ot'][1]['CertificationID']).'"  onclick="pageRedirect(this)" class="btn btn-primary btn-xs view" ><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button>';
		            		}
		            	}
		            	$subdata1National[] = $actionLink;

		            } else {
		            	if($access_view){
		            		$actionLink.='<button data-href="'.base_url().'certification/certificationview/'.encryptor($certificationStatus['lr'][1]['CertificationID']).'"  onclick="pageRedirect(this)" class="btn btn-primary btn-xs view" ><i class="glyphicon glyphicon-check" aria-hidden="true"></i> View</button>';
		            	}
		            	$subdata1National[] = $actionLink;
		            }
	            } else {
	            	// not elegible for ot national certification
	            	//$subdata1National[] = $cnt_data.'.'.$cnt_data_in++;
					if($searchData['RoleName']=='Ministry'){
		            	$subdata1National[] = $value['StateName'];
		        	}
		        	if($searchData['RoleName']=='State' || $searchData['RoleName']=='Ministry'){
		            	$subdata1National[] = $value['DistrictName'];
		            }
		            if($searchData['RoleName']=='District' || $searchData['RoleName']=='State' || $searchData['RoleName']=='Ministry' ){
		            	$subdata1National[] = $value['FacilityName'];
		            }
		            $subdata1National[] = 'National Certification';
		            $subdata1National[] = 'Operation Theater';
		            $subdata1National[] = 'Not Eligible';
		            $actionLink='';
		            if($access_eligibility){
		            	$actionLink.='<button data-href="'.base_url().$checkListUrl.'/'.$facilityUrl.'/national"  onclick="pageRedirect(this)" class="btn btn-primary btn-xs getScore   view-eligility" ><i class="fa fa-bar-chart"></i> View Eligibility Criteria</button>';
		            }
		            $subdata1National[] = $actionLink;
	            }

            }
            // National both lr&ot ended
            	if(!empty($subdata)){ array_unshift($subdata , $cnt_data.'.'.$cnt_data_in++);   $data[] = $subdata; }
	            if(!empty($subdata1)){ array_unshift($subdata1 , $cnt_data.'.'.$cnt_data_in++); $data[] = $subdata1; }
	            if(!empty($subdataNational)){ array_unshift($subdataNational , $cnt_data.'.'.$cnt_data_in++); $data[] = $subdataNational; }
	            if(!empty($subdata1National)){ array_unshift($subdata1National , $cnt_data.'.'.$cnt_data_in++); $data[] = $subdata1National; }
		}

		return array('totalData'=>$queryTot->num_rows(),'totalFilter'=>$queryTot->num_rows(),'data'=>$data);
	}
	function getdata($user){
		// facility name
		// lr and ot score in peer assesment
		//

		$sqlLR="SELECT f.FacilityName,a.AnswerID as ansId
from usermapping as um
INNER JOIN facilities as f ON(f.FacilityID=um.FacilityID AND um.FacilityID>0)
LEFT JOIN answer as a on(a.UserID=um.UserID AND a.SurveyStatus='1' AND a.IsActive='1' AND a.SurveyID='1'  )
WHERE 1 AND um.FacilityID>'0' AND um.UserID='".$user."' 
order by a.AnswerID DESC 
limit 1
";
		$queryLR =$this->db->query($sqlLR,NULL);
		$dataLR=$queryLR->row_array();
		$data['FacilityName']=$dataLR['FacilityName'];

		$sqlOT="SELECT f.FacilityName,a.AnswerID as ansId
from usermapping as um
INNER JOIN facilities as f ON(f.FacilityID=um.FacilityID AND um.FacilityID>0)
INNER JOIN answer as a on(a.UserID=um.UserID AND a.SurveyStatus='1' AND a.IsActive='1' AND a.SurveyID='2'  )
WHERE 1 AND um.FacilityID>'0' AND um.UserID='".$user."' 
order by a.AnswerID DESC 
limit 1
";
		$queryOT =$this->db->query($sqlOT,NULL);
		$dataOT=$queryOT->row_array();

		if(!empty($dataLR['ansId'])){
			$sql="select survey.SurveyDesc,survey.SurveyName,c.CategoryCode,c.CategoryName,count(q.QuestionID) as quesTot,sum(ad.Answer) as answer,ROUND(((sum(ad.Answer)/(count(q.QuestionID)*2) )*100)) as score 
		  		from question q 
		  		INNER join subcategory s on(q.SubcategoryID=s.SubcategoryID) 
		  		INNER join category c on (s.CategoryID=c.CategoryID) 
		  		INNER JOIN survey on(c.SurveyID=survey.SurveyID) 
		  		INNER join answer a on(survey.SurveyID=a.SurveyID ) 
		  		INNER join answerdetail ad on(a.AnswerID=ad.AnswerID AND ad.QuestionID=q.QuestionID AND ad.IsActive='1') 
		  		WHERE a.AnswerID='".$dataLR['ansId']."' 
		  		GROUP BY a.AnswerID 
		  		ORDER by c.CategoryID ASC,s.SubcategoryID ASC,q.Serial ASC";
		    $result = $this->db->query($sql, NULL);
		    $data1=$result->row_array();
		    $data['lr']=$data1['score'];
		}

		if(!empty($dataOT['ansId'])){
			$sql="select survey.SurveyDesc,survey.SurveyName,c.CategoryCode,c.CategoryName,count(q.QuestionID) as quesTot,sum(ad.Answer) as answer,ROUND(((sum(ad.Answer)/(count(q.QuestionID)*2) )*100)) as score 
		  		from question q 
		  		INNER join subcategory s on(q.SubcategoryID=s.SubcategoryID) 
		  		INNER join category c on (s.CategoryID=c.CategoryID) 
		  		INNER JOIN survey on(c.SurveyID=survey.SurveyID) 
		  		INNER join answer a on(survey.SurveyID=a.SurveyID ) 
		  		INNER join answerdetail ad on(a.AnswerID=ad.AnswerID AND ad.QuestionID=q.QuestionID AND ad.IsActive='1') 
		  		WHERE a.AnswerID='".$dataOT['ansId']."' 
		  		GROUP BY a.AnswerID 
		  		ORDER by c.CategoryID ASC,s.SubcategoryID ASC,q.Serial ASC";
		    $result = $this->db->query($sql, NULL);
		    $data2=$result->row_array();
		    $data['ot']=$data2['score'];
		}
	  	
	    return $data;
	}

	function getApprovalData($searchData){

        $data = array();
        $col = array(
            0 => 'FacilityName',
        );
	  	$this->db->select('f.facilityName');
	    $this->db->from('certification');
		$this->db->join('usermapping um','certification.UserID=um.UserID');
		$this->db->join('facilities f', 'f.FacilityID=um.FacilityID AND um.FacilityID>0 AND f.IsActive=1', 'inner');
		$this->db->join('states s','f.StateID=s.StateID AND f.IsActive=1', 'inner');
		$this->db->join('district d','f.DistrictID=d.DistrictID AND f.IsActive=1', 'inner');
		if(!empty($searchData['cond'])){
			$this->db->where_in($searchData['cond']['mappedField'],$searchData['cond']['mappedData']);
		}
	    $this->db->where('certification.IsActive','1');
	    $this->db->where('certification.IsCurrent','1');
	    $this->db->where('certification.certificationStatus','1');
	    $this->db->where_in('certification.level',array('1','2'));
        if(!empty($searchData['search']['value'])){
            $searchString=trim($searchData['search']['value']);
            // datatable search conditions
            if(strtolower($searchString)=='incomplete'){
                //$this->db->where("(answer.SurveyStatus='0')", NULL, FALSE);
            } else {
                $this->db->where("(f.FacilityName like '%".$searchString."%' OR d.DistrictName like '%".$searchString."%' OR s.StateName like '%".$searchString."%')", NULL, FALSE);
            }
        }
        if(!empty($searchData['search_state'])){
        	$this->db->where('s.StateID',$searchData['search_state']);
        }
        if(!empty($searchData['search_district'])){
        	$this->db->where('d.DistrictID',$searchData['search_district']);
        }
        if(!empty($searchData['search_facility'])){
        	$this->db->where('certification.UserID',$searchData['search_facility']);
        }
	    $queryTot = $this->db->get();





	    $this->db->select('f.FacilityName,s.StateName,d.DistrictName,certification.CertificationID,certification.certification_type,certification.level,certification.status');
	    $this->db->from('certification');
		$this->db->join('usermapping um','certification.UserID=um.UserID');
		$this->db->join('facilities f', 'f.FacilityID=um.FacilityID AND um.FacilityID>0 AND f.IsActive=1', 'inner');
		$this->db->join('states s','f.StateID=s.StateID AND f.IsActive=1', 'inner');
		$this->db->join('district d','f.DistrictID=d.DistrictID AND f.IsActive=1', 'inner');
		if(!empty($searchData['cond'])){
		$this->db->where_in($searchData['cond']['mappedField'],$searchData['cond']['mappedData']);
		}
	    $this->db->where('certification.IsActive','1');
	    $this->db->where('certification.IsCurrent','1');
	    $this->db->where('certification.certificationStatus','1');
	    $this->db->where_in('certification.level',array('1','2'));
        if(!empty($searchData['search']['value'])){
            $searchString=trim($searchData['search']['value']);
            
            if(strtolower($searchString)=='incomplete'){
                //$this->db->where("(answer.SurveyStatus='0')", NULL, FALSE);
            } else {
                $this->db->where("(f.FacilityName like '%".$searchString."%' OR d.DistrictName like '%".$searchString."%' OR s.StateName like '%".$searchString."%')", NULL, FALSE);
            }
        }
        if(!empty($searchData['search_state'])){
        	$this->db->where('s.StateID',$searchData['search_state']);
        }
        if(!empty($searchData['search_district'])){
        	$this->db->where('d.DistrictID',$searchData['search_district']);
        }
        if(!empty($searchData['search_facility'])){
        	$this->db->where('certification.UserID',$searchData['search_facility']);
        }
        //$this->db->group_by('answer.UserID');
	    $this->db->order_by($col[$searchData['order'][0]['column']], $searchData['order'][0]['dir']);
	    $this->db->limit($this->input->post('length'),$this->input->post('start'));
	    $query = $this->db->get();
	    $cnt=$searchData['start'];

        //$access_add=$this->CommonModel->checkPageActionWeb('facility/index','access_add',$this->session->userdata('RoleName'));
        $access_edit=$this->CommonModel->checkPageActionWeb('certification/approval','access_edit',$this->session->userdata('RoleName'));
        //$access_delete=$this->CommonModel->checkPageActionWeb('facility/index','access_delete',$this->session->userdata('RoleName'));
        $access_view=$this->CommonModel->checkPageActionWeb('certification/approval','access_view',$this->session->userdata('RoleName'));
		foreach ($query->result_array() as $key => $value) {
			$subdata=array();
			$subdata[]=++$cnt;
			switch ($value['certification_type']) {
				case 'lr':
					$certification_type='Labour Room';
					break;
				case 'ot':
					$certification_type='Operation Theater';
					break;
				case 'both':
					$certification_type='Labour Room & Operation Theater';
					break;	
				default:
					$certification_type='Labour Room';
					break;
			}
			if($searchData['RoleName']=='Ministry'){
            	$subdata[] = $value['StateName'];
        	}
        	if($searchData['RoleName']=='State' || $searchData['RoleName']=='Ministry'){
            	$subdata[] = $value['DistrictName'];
            }
            if($searchData['RoleName']=='District' || $searchData['RoleName']=='State' || $searchData['RoleName']=='Ministry' ){
            	$subdata[] = $value['FacilityName'];
            }
            $subdata[] = $this->config->item('certificationLevel')[$value['level']];
            $subdata[] = $certification_type;
            $subdata[] = $this->config->item('certificationStatus')[$value['status']];
            $actionLink='';
            $editBtn='<button data-href="'.base_url().'certification/approvaledit/'.encryptor($value['CertificationID']).'"  onclick="pageRedirect(this)" class="btn btn-primary btn-xs view edit-btn-new" >Edit </button>';
            $viewBtn='<button data-href="'.base_url().'certification/approvalview/'.encryptor($value['CertificationID']).'"  onclick="pageRedirect(this)" class="btn btn-primary btn-xs view" >View </button>';
            switch ($value['status']) {
            	case 1:
            	case 2:
            	case 4:
            	case 5:
            		if($access_edit){
            			$actionLink.=$editBtn;
            		}
            		if($access_view){
            			$actionLink.=$viewBtn;
            		}
            		$subdata[] = $actionLink;
            		break;
            	case 3:
            	case 6:
            	case 7:
            	case 8:
            		if($access_view){
            			$actionLink.=$viewBtn;
            		}
            		$subdata[] = $actionLink;
            		break;
            	default:
            		if($access_edit){
            			$actionLink.=$editBtn;
            		}
            		if($access_view){
            			$actionLink.=$viewBtn;
            		}
            		$subdata[] = $actionLink;
            		break;
            }
            $data[] = $subdata;
		}

		return array('totalData'=>$queryTot->num_rows(),'totalFilter'=>$queryTot->num_rows(),'data'=>$data);
	}

	function get_certification_data($CertificationID,$userID){
		$data=array();
		if(!empty($CertificationID)){
			$this->db->select('*');
			$this->db->from('certification');
			$this->db->where('CertificationID',$CertificationID);
			$query = $this->db->get();
			if($query->num_rows()==1){
				$data['main']=$query->row_array();
			}
			$this->db->select('*');
			$this->db->from('certification_otherdoc');
			$this->db->where('CertificationID',$CertificationID);
			$query = $this->db->get();
			if($query->num_rows()>0){
				$data['othername']=$query->result_array();
			}			
			$this->db->select('*');
			$this->db->from('certification_anexture');
			$this->db->where('CertificationID',$CertificationID);
			$this->db->order_by('monthseq','ASC');
			$query = $this->db->get();
			if($query->num_rows()>0){
				$data['anexturedoc']=$query->result_array();
			}
		}

	    $this->db->select('facilities.FacilityName,facilities.services,facilities.FacilityNumber,facilities.Address,facilities.PinCode,facilities.landLine');
	    $this->db->from('usermapping');
	    $this->db->join('facilities', 'usermapping.FacilityID=facilities.FacilityID AND usermapping.FacilityID>0', 'inner');
	    //$this->db->join('incharge main', 'usermapping.UserID=main.UserID AND main.IsActive="1" AND main.TypeDetailID="438" ', 'left');
	    $this->db->where('usermapping.UserID',$userID);
	    $this->db->limit('1');
	    $query = $this->db->get();
		if($query->num_rows()>0){
			$data['show']=$query->row_array();
		}

		return $data;
	}

	function get_certification_dataview($CertificationID){
		$data=array();
		if(!empty($CertificationID)){
			$this->db->select('*');
			$this->db->from('certification');
			$this->db->where('CertificationID',$CertificationID);
			$query = $this->db->get();
			if($query->num_rows()==1){
				$data['main']=$query->row_array();
			}
			$this->db->select('*');
			$this->db->from('certification_otherdoc');
			$this->db->where('CertificationID',$CertificationID);
			$query = $this->db->get();
			if($query->num_rows()>0){
				$data['othername']=$query->result_array();
			}
			$this->db->select('*');
			$this->db->from('certification_anexture');
			$this->db->where('CertificationID',$CertificationID);
			$this->db->order_by('monthseq','ASC');
			$query = $this->db->get();
			if($query->num_rows()>0){
				$data['anexturedoc']=$query->result_array();
			}
		    $this->db->select('facilities.FacilityName,facilities.services,facilities.FacilityNumber,facilities.Address,facilities.PinCode,facilities.landLine');
		    $this->db->from('usermapping');
		    $this->db->join('facilities', 'usermapping.FacilityID=facilities.FacilityID AND usermapping.FacilityID>0', 'inner');
		    //$this->db->join('incharge main', 'usermapping.UserID=main.UserID AND main.IsActive="1" AND main.TypeDetailID="438" ', 'left');
		    $this->db->where('usermapping.UserID',$data['main']['userID']);
		    $this->db->limit('1');
		    $query = $this->db->get();
			if($query->num_rows()>0){
				$data['show']=$query->row_array();
			}
			$this->db->select('*');
			$this->db->from('certification_logs');
			$this->db->where('CertificationID',$CertificationID);
			$query = $this->db->get();
			if($query->num_rows()>0){
				$data['certification_logs']=$query->result_array();
			}
			$this->db->select('certification_status.*,users.FirstName as UserName');
			$this->db->from('certification_status');
			$this->db->join('users', 'users.UserID = certification_status.CreatedBy', 'inner');
			$this->db->where('CertificationID',$CertificationID);
			$query = $this->db->get();
			if($query->num_rows()>0){
				$data['certification_status']=$query->result_array();
			}

			if(!empty($data['main']['dqauPeerAssmentScoreID'])){
				$this->db->select('SurveyID');
				$this->db->from('answer');
				$this->db->where('AnswerID',$data['main']['dqauPeerAssmentScoreID']);
				$query = $this->db->get();
				if($query->num_rows()>0){
					$data['assessment']=$query->row_array();
					$data['assessment']['dqauPeerAssmentScoreID']=$data['main']['dqauPeerAssmentScoreID'];
				}				
			}

		}



		return $data;
	}

	function save($data){
		$response=array('code'=>26,'msg'=>$this->config->item('errCodes')[26]);

		if(empty($data['CertificationID'])){
			if($data['type']=='lr' ){
			  	$this->db->select('a.AnswerID');
			    $this->db->from('answer a');
			    $this->db->where('a.UserID',$data['userID']);
			    $this->db->where('a.SurveyID','1');
			    $this->db->where('a.SurveyStatus','1');
			    $this->db->where('a.IsActive','1');
			    $this->db->order_by('a.AnswerID', 'DESC');
			    $this->db->limit('1');
			    $queryAssementScoreLR = $this->db->get();
			    if($queryAssementScoreLR->num_rows()>0){
			    	$queryAssementScoreLRData=$queryAssementScoreLR->row_array();
			    	$data['main']['facilityPeerAssmentLR']=$queryAssementScoreLRData['AnswerID'];
			    }
			}
			if($data['type']=='ot' ){
			  	$this->db->select('a.AnswerID');
			    $this->db->from('answer a');
			    $this->db->where('a.UserID',$data['userID']);
			    $this->db->where('a.SurveyID','2');
			    $this->db->where('a.SurveyStatus','1');
			    $this->db->where('a.IsActive','1');
			    $this->db->order_by('a.AnswerID', 'DESC');
			    $this->db->limit('1');
			    $queryAssementScoreOT = $this->db->get();
			    if($queryAssementScoreOT->num_rows()>0){
			    	$queryAssementScoreOTData=$queryAssementScoreOT->row_array();
			    	$data['main']['facilityPeerAssmentOT']=$queryAssementScoreOTData['AnswerID'];
			    }				
			}


			$data['main']['applied_date']=date('Y-m-d H:i:s');
			$data['main']['certification_no']=date('Y/m/d').'-'.rand(10,100);
            $data['main']['userID']=$data['userID'];
            $data['main']['level']=$data['level'];
            $data['main']['certification_type']=$data['type'];
			$data['main']['IsCurrent']='1';
			$data['main']['IsActive']='1';
			$data['main']['status']='1';
			$data['main']['CreatedOn']=date('Y-m-d H:i:s');
			$data['main']['CreatedBy']=$data['user'];
			$this->db->insert('certification', $data['main']);
			$CertificationID=$this->db->insert_id();
			if($CertificationID>0){
				if(!empty($data['otherdoc'])){
					foreach ($data['otherdoc'] as $key => $value) {
						$data['otherdoc'][$key]['IsActive']='1';
						$data['otherdoc'][$key]['CreatedOn']=date('Y-m-d H:i:s');
						$data['otherdoc'][$key]['CreatedBy']=$data['user'];
						$data['otherdoc'][$key]['CertificationID']=$CertificationID;
					}
					$this->db->insert_batch('certification_otherdoc', $data['otherdoc']);
				}

				if(!empty($data['anexturedoc'])){
					foreach ($data['anexturedoc'] as $key => $value) {
						$data['anexturedoc'][$key]['CertificationID']=$CertificationID;
						$this->db->insert('certification_anexture', $data['anexturedoc'][$key]);
					}
				}

				$logInsert=array(
					'msg'=>'Certification applied',
					'facility'=>'1',
					'district'=>'1',
					'state'=>'1',
					'ministry'=>'1',
					'CertificationID'=>$CertificationID,
					'UserID'=>$data['userID'],
					'CreatedOn'=>date('Y-m-d H:i:s'),
				);
				$this->db->insert('certification_logs', $logInsert);

				$data['sts']['remarks']='Certification Applied';
				$data['sts']['new_status']=$data['main']['status'];
				$data['sts']['old_status']='1';
				$data['sts']['CreatedOn']=date('Y-m-d H:i:s');
				$data['sts']['CreatedBy']=$data['user'];
				$data['sts']['CertificationID']=$CertificationID;
				$this->db->insert('certification_status', $data['sts']);

				// insert success
				$response['code']=0;
				$response['msg']=$this->config->item('errCodes')[0];
				$response['CertificationID']=encryptor($CertificationID);
			} else {
				$response['code']=11;
				$response['msg']=$this->config->item('errCodes')[11];
			}

		} else {
			$this->db->select('status');
			$this->db->from('certification');
			$this->db->where('CertificationID',$data['CertificationID']);
			$query = $this->db->get();
			$dataCertificate=$query->row_array();
			if($query->num_rows()>0 && @$dataCertificate['status']=='1' || @$dataCertificate['status']=='5' ){
				$this->db->where('CertificationID', $data['CertificationID']);
				if(@$dataCertificate['status']=='5' && $data['main']['certificationStatus']=='1'){
					$data['main']['status']='1';
				}
				if ($this->db->update('certification', $data['main']) === FALSE){
				    // not updated
					$response['code']=11;
					$response['msg']=$this->config->item('errCodes')[11];
				} else {
				    // updated
					if(!empty($data['otherdoc'])){
						foreach ($data['otherdoc'] as $key => $value) {
							$data['otherdoc'][$key]['IsActive']='1';
							$data['otherdoc'][$key]['CreatedOn']=date('Y-m-d H:i:s');
							$data['otherdoc'][$key]['CreatedBy']=$data['user'];
							$data['otherdoc'][$key]['CertificationID']=$data['CertificationID'];
						}
						$this->db->insert_batch('certification_otherdoc', $data['otherdoc']); 					
					}
					if(!empty($data['othernameSaved'])){
						foreach ($data['othernameSaved'] as $key => $value) {
							$dataDoc=array();
							$dataDoc['name']=$value;
							$dataDoc['ModifiedOn']=date('Y-m-d H:i:s');
							$dataDoc['ModifiedBy']=$data['user'];
							$this->db->where('certification_otherdocID', $data['certification_otherdocID'][$key]);
							$this->db->update('certification_otherdoc', $dataDoc);
							
						}						
					}

					if(!empty($data['anexturedoc'])){
						foreach ($data['anexturedoc'] as $key => $value) {
							$updateData=array();
							$updateData['submitdate']=$data['anexturedoc'][$key]['submitdate'];
							if(!empty($data['anexturedoc'][$key]['submitfile'])){
								$updateData['submitfile']=$data['anexturedoc'][$key]['submitfile'];
							}
							$whereCond=array(
								'CertificationID'=>$data['CertificationID'],
								'monthseq'=>$data['anexturedoc'][$key]['monthseq']
							);
							$this->db->where($whereCond);
							$this->db->update('certification_anexture', $updateData);
						}
					}

					if(!empty($data['certification_otherdocID'])){
						$this->db->where_not_in('certification_otherdocID', $data['certification_otherdocID']);
						$this->db->delete('certification_otherdoc');						
					}
					if($dataCertificate['status']==5){
						$logInsert=array(
							'msg'=>'Certification Updated',
							'facility'=>'1',
							'district'=>'1',
							'state'=>'1',
							'ministry'=>'1',
							'CertificationID'=>$data['CertificationID'],
							'UserID'=>$data['userID'],
							'CreatedOn'=>date('Y-m-d H:i:s'),
						);
						$this->db->insert('certification_logs', $logInsert);
					}
				
					$response['code']=0;
					$response['msg']=$this->config->item('errCodes')[27];
				}
			} else {
				// not editable
				$response['code']=26;
				$response['msg']=$this->config->item('errCodes')[26];
			}
		}
		if($data['main']['certificationStatus']=='1'){
			/*if($type=='ins'){

			} else {

			}*/
		    $this->db->select('incharge.FirstName,incharge.LastName,incharge.Email');
		    $this->db->from('users');
		    $this->db->join('incharge', 'users.UserID=incharge.UserID AND incharge.TypeDetailID=438 AND incharge.IsActive=1', 'left');
		    $this->db->where('users.UserName',$data['userID']);
		    $this->db->where('users.IsActive','1');
		    $this->db->limit('1');
		    $query = $this->db->get();
		    if($query->num_rows()>0){
			    $userdata=$query->row();
		        $ci = get_instance();
		        $dataMail=array();
		        $dataMail['email']=$userdata->Email;
		        $dataMail['name']=$userdata->FirstName.' '.$userdata->LastName;
		        $dataMail['pswrd']=$password;
		        $msg=$this->EmailModel->certification_insert($dataMail);
		        $ci->load->library('email');
		        $config['protocol'] = $this->config->item('email_protocol');
		        $config['smtp_host'] = $this->config->item('email_smtp_host');
		        $config['smtp_port'] = $this->config->item('email_smtp_port');
		        $config['smtp_user'] = $this->config->item('email_smtp_user');
		        $config['smtp_pass'] = $this->config->item('email_smtp_pass');
		        $config['charset'] = $this->config->item('email_charset');
		        $config['mailtype'] = $this->config->item('email_mailtype');
		        $config['newline'] = $this->config->item('email_newline');
		        $ci->email->initialize($config);
		        $ci->email->from($this->config->item('email_from_main'), $this->config->item('email_name_main'));
		        $ci->email->to($userdata->Email);
		        $ci->email->subject('Laqshya password reset');
		        $ci->email->message($msg);		       
				$ci->email->send();
		    }

		}

		return $response;
	}


	function get_approvalview($CertificationID){
		$data=array();
		if(!empty($CertificationID)){
			$this->db->select('*');
			$this->db->from('certification');
			$this->db->where('CertificationID',$CertificationID);
			$query = $this->db->get();
			if($query->num_rows()==1){
				$data['main']=$query->row_array();
			}
			$this->db->select('*');
			$this->db->from('certification_otherdoc');
			$this->db->where('CertificationID',$CertificationID);
			$query = $this->db->get();
			if($query->num_rows()>0){
				$data['othername']=$query->result_array();
			}
			
			$this->db->select('*');
			$this->db->from('certification_anexture');
			$this->db->where('CertificationID',$CertificationID);
			$this->db->order_by('monthseq','ASC');
			$query = $this->db->get();
			if($query->num_rows()>0){
				$data['anexturedoc']=$query->result_array();
			}
			
		    $this->db->select('facilities.FacilityName,facilities.services,facilities.FacilityNumber,facilities.Address,facilities.PinCode,facilities.landLine');
		    $this->db->from('usermapping');
		    $this->db->join('facilities', 'usermapping.FacilityID=facilities.FacilityID AND usermapping.FacilityID>0', 'inner');
		    //$this->db->join('incharge main', 'usermapping.UserID=main.UserID AND main.IsActive="1" AND main.TypeDetailID="438" ', 'left');
		    $this->db->where('usermapping.UserID',$data['main']['userID']);
		    $this->db->limit('1');
		    $query = $this->db->get();
			if($query->num_rows()>0){
				$data['show']=$query->row_array();
			}
			$this->db->select('*');
			$this->db->from('certification_logs');
			$this->db->where('CertificationID',$CertificationID);
			$query = $this->db->get();
			if($query->num_rows()>0){
				$data['certification_logs']=$query->result_array();
			}
			$this->db->select('certification_status.*,users.FirstName as UserName');
			$this->db->from('certification_status');
			$this->db->join('users', 'users.UserID = certification_status.CreatedBy', 'inner');
			$this->db->where('CertificationID',$CertificationID);
			$query = $this->db->get();
			if($query->num_rows()>0){
				$data['certification_status']=$query->result_array();
			}
			

		}



		return $data;
	}
	function get_approvaledit($CertificationID){
		$data=array();
		if(!empty($CertificationID)){
			$this->db->select('certification.*,aLR.format as formatLR,,aOT.format as formatOT,');
			$this->db->from('certification');
			$this->db->join('answer aLR', 'certification.facilityPeerAssmentLR=aLR.AnswerID', 'left');
			$this->db->join('answer aOT', 'certification.facilityPeerAssmentOT=aOT.AnswerID', 'left');
			$this->db->where('CertificationID',$CertificationID);
			$query = $this->db->get();
			if($query->num_rows()==1){
				$data['main']=$query->row_array();
			}
			$this->db->select('*');
			$this->db->from('certification_otherdoc');
			$this->db->where('CertificationID',$CertificationID);
			$query = $this->db->get();
			if($query->num_rows()>0){
				$data['othername']=$query->result_array();
			}
			$this->db->select('*');
			$this->db->from('certification_anexture');
			$this->db->where('CertificationID',$CertificationID);
			$this->db->order_by('monthseq','ASC');
			$query = $this->db->get();
			if($query->num_rows()>0){
				$data['anexturedoc']=$query->result_array();
			}
		    $this->db->select('facilities.FacilityName,facilities.services,facilities.FacilityNumber,facilities.Address,facilities.PinCode,facilities.landLine');
		    $this->db->from('usermapping');
		    $this->db->join('facilities', 'usermapping.FacilityID=facilities.FacilityID AND usermapping.FacilityID>0', 'inner');
		    //$this->db->join('incharge main', 'usermapping.UserID=main.UserID AND main.IsActive="1" AND main.TypeDetailID="438" ', 'left');
		    $this->db->where('usermapping.UserID',$data['main']['userID']);
		    $this->db->limit('1');
		    $query = $this->db->get();
			if($query->num_rows()>0){
				$data['show']=$query->row_array();
			}
			$this->db->select('*');
			$this->db->from('certification_logs');
			$this->db->where('CertificationID',$CertificationID);
			$query = $this->db->get();
			if($query->num_rows()>0){
				$data['certification_logs']=$query->result_array();
			}
			$this->db->select('certification_status.*,users.FirstName as UserName');
			$this->db->from('certification_status');
			$this->db->join('users', 'users.UserID = certification_status.CreatedBy', 'inner');
			$this->db->where('CertificationID',$CertificationID);
			$query = $this->db->get();
			if($query->num_rows()>0){
				$data['certification_status']=$query->result_array();
			}


		}



		return $data;
	}

	function savestatus($data){		
		if(in_array($data['main']['status'], array('3','6','7','8'))){
			$data['main']['certification_date']=date('Y-m-d');
		}
		$data['main']['ModifiedOn']=date('Y-m-d H:i:s');
		$data['main']['ModifiedBy']=$data['user'];
		$this->db->where('CertificationID', $data['CertificationID']);
		if ($this->db->update('certification', $data['main']) === FALSE){
		    // not updated
			$response['code']=11;
			$response['msg']=$this->config->item('errCodes')[11];
		} else {
		    // updated

			$data['sts']['remarks']=$data['main']['remarks'];
			$data['sts']['new_status']=$data['main']['status'];
			$data['sts']['old_status']='1';
			$data['sts']['CreatedOn']=date('Y-m-d H:i:s');
			$data['sts']['CreatedBy']=$data['user'];
			$data['sts']['CertificationID']=$data['CertificationID'];
			$this->db->insert('certification_status', $data['sts']);

			$logInsert=array(
				'msg'=>'Certification Status Changed to '.$this->config->item('certificationStatus')[$data['main']['status']],
				'facility'=>'1',
				'district'=>'1',
				'state'=>'1',
				'ministry'=>'1',
				'CertificationID'=> $data['CertificationID'],
				'UserID'=>$data['user'],
				'CreatedOn'=>date('Y-m-d H:i:s'),
			);
			$this->db->insert('certification_logs', $logInsert);		
			$response['code']=0;
			$response['msg']=$this->config->item('errCodes')[27];
		}
		return $response;
	}



}