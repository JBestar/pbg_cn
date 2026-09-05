<?php namespace App\Controllers;

use App\Models\LogHist_Model;
use App\Models\Captcha_Model;
use App\Models\ConfGame_Model;
use App\Models\PbRound_Model;
use App\Models\PbBet_Model;
use App\Models\MoneyHist_Model;
use App\Models\Charge_Model;
use App\Models\Exchange_Model;
use App\Models\Clean_Model;
use App\Models\Sess_Model;

class Api extends BaseController
{
    
	public function index()
	{
	}

    public function login(){
        $jsonData = $_REQUEST['json_'];
		$arrRqData = json_decode($jsonData, true);
		
        $uid = $arrRqData['uid'];
		$pwd = $arrRqData['pwd'];
		$captcha = $arrRqData['captcha'];
		$img = $arrRqData['img'];

        $loghist_model = new LogHist_Model();
		$captcha_model = new Captcha_Model();

        $objMember = $this->member_model->login($uid, $pwd);

		$captchaRes = $captcha_model->verify($img, $captcha);
		
		if( $captchaRes != RESULT_OK){
			$arrResult['code'] = $captchaRes;			//캡쳐오류
			$arrResult['status'] = STATUS_FAIL;
		}
        else if(is_null($objMember)){
			$arrResult['code'] = RESULT_FAIL;			//아이디,비번오류
			$arrResult['status'] = STATUS_FAIL;
		} else if($objMember->mb_state_delete == 1 ){	//삭제 
			$arrResult['code'] = RESULT_FAIL;
			$arrResult['status'] = STATUS_FAIL;
		} else if($objMember->mb_level < LEVEL_AGENCY){	//매장 오류 
			$arrResult['code'] = RESULT_FAIL;
			$arrResult['status'] = STATUS_FAIL;
		} else if( !$this->member_model->permittedMember($objMember) ){	//차단
			$arrResult['code'] = RESULT_STOP;
			$arrResult['status'] = STATUS_FAIL;
		} 
		else {
			$this->sess_model->deleteLast();
			$sessId = $this->session->session_id;
			// writeLog("Login SessId=".$sessId);
			$objMember->mb_ip_last = $this->request->getIPAddress();
			
			$arrSess = $this->sess_model->getByUid($objMember->mb_uid);
			$bDupLogin = false;
			if(is_null($arrSess)){
				$bDupLogin = false;
			} else {

				foreach($arrSess as $key => $objSess){
					if(strcmp($objSess->sess_id, $sessId) == 0) {
						$this->sess_model->deleteById($objSess->sess_id);
						unset($arrSess[$key]);
					}
				}
				if(count($arrSess) > 1 ){
					$bDupLogin = true;
				}
			} 

			if($bDupLogin){
				$arrResult['code'] = RESULT_EXIST_ID;
				$arrResult['status'] = STATUS_FAIL;    
			} else {
				///Update Last Time                                    
				$this->member_model->updateLast($objMember);
				$sessData = array(
					'uid'=>$objMember->mb_uid,
					'logged_in'=>TRUE,
					'locale'=> isset($objMember->mb_lang) && $objMember->mb_lang ? $objMember->mb_lang : 'ko',
				);
				$this->session->set($sessData);

				$sess_id = $this->session->session_id;
				$this->sess_model->login($objMember, $sess_id);
				$loghist_model->addLog($objMember);	

				$arrResult['code'] = RESULT_OK;
				$arrResult['status'] = STATUS_SUCCESS;
			}
        	
		} 
		echo json_encode($arrResult);

    }

	public function logout(){

		$this->sess_model->deleteById($this->session->session_id);
		$this->session->destroy();
		
		$arrResult['status'] = "success";
		echo json_encode($arrResult);
	}

	public function set_lang()
	{
		$arrResult = ['status' => STATUS_FAIL, 'code' => RESULT_FAIL];
		if (!is_login()) {
			$arrResult['status'] = STATUS_LOGOUT;
			echo json_encode($arrResult);
			return;
		}
		$jsonData = isset($_REQUEST['json_']) ? $_REQUEST['json_'] : '';
		$arrRqData = json_decode($jsonData, true);
		$lang = isset($arrRqData['lang']) ? strtolower(trim($arrRqData['lang'])) : 'ko';
		if (!in_array($lang, ['ko', 'zh', 'en'], true)) {
			$lang = 'ko';
		}
		$uid = $this->session->uid;
		if ($this->member_model->updateLang($uid, $lang)) {
			$this->session->set('locale', $lang);
			service('request')->setLocale($lang);
			$arrResult['status'] = STATUS_SUCCESS;
			$arrResult['code'] = RESULT_OK;
			$arrResult['lang'] = $lang;
		}
		echo json_encode($arrResult);
	}

    public function assets(){ 

        $result = new \StdClass;
		if(!is_login())
		{
			$result->status = STATUS_LOGOUT;
		}
		else {

			$uid = $this->session->uid;
            $objMember = $this->member_model->getByUid($uid);
			$sessId = $this->session->session_id;

            $bPermit = true;
			if(is_null($objMember))
				$bPermit = false;
			else if( !$this->member_model->permittedMember($objMember) )
				$bPermit = false;
			else if( is_null($this->sess_model->getById($sessId)) )
				$bPermit = false;

			if(!$bPermit){
				$this->sess_model->deleteById($sessId);
				$this->session->destroy();
                $result->status = STATUS_LOGOUT;                
			} else{
				
				$objUser = new \StdClass;
                $objUser->mb_uid = $objMember->mb_uid;
                $objUser->mb_nickname = $objMember->mb_nickname;
				$objUser->mb_level = $objMember->mb_level;
				$objUser->mb_game_pb_ratio = 0;
				if($objMember->mb_level <= LEVEL_AGENCY){
					$objUser->mb_money = $objMember->mb_money;
					$objUser->mb_point = $objMember->mb_point;
					$objUser->mb_game_pb_ratio = $objMember->mb_game_pb_ratio;
				} else {
					$objAsset = $this->member_model->getTotalAssets();
					if(!is_null($objAsset)){
						$objUser->mb_money = $objAsset->money_sum;
						$objUser->mb_point = $objAsset->point_sum;
					} else {
						$objUser->mb_money = 0;
						$objUser->mb_point = 0;
					}
				}
				
				$objUser->mb_ip_last = $this->request->getIPAddress();
				$this->sess_model->updateLast($sessId);

				$result->data = $objUser;
				$result->status = STATUS_SUCCESS;

			} 

        }

        echo json_encode($result);	
    }


	public function point_change()
	{
		
		$result = new \StdClass;
		if(!is_login())
		{
            $result->status = STATUS_LOGOUT;		
        } else {
			$uid = $this->session->uid;
			$objMember = $this->member_model->getByUid($uid);

			$moneyhist_model = new MoneyHist_Model();

			if($objMember->mb_point > 0 ) {
				$this->member_model->updateAssets($objMember->mb_fid, $objMember->mb_point, 0-$objMember->mb_point);
				$moneyhist_model->registerPointToMoney($objMember, $objMember->mb_point);
			}			

			$result->status = STATUS_SUCCESS;
        }
		
		echo json_encode($result);

    }


	public function account_today()
	{
		
		$result = new \StdClass;
		if(!is_login())
		{
            $result->status = STATUS_LOGOUT;		
        } else {
			$uid = $this->session->uid;
			$objMember = $this->member_model->getAllByUid($uid);
			$moneyhist_model = new MoneyHist_Model();
			
			$sToday = date('Y-m-d');
			$arrReqData['start'] = $sToday;
			$arrReqData['end'] = $sToday;
			if($objMember->mb_level == LEVEL_AGENCY)
				$arrReqData['mb_emp_fid'] = $objMember->mb_fid;
			//else $arrReqData['mb_emp_fid'] = 0;

			$arrAcc = [null, null];
			$arrDateAcc = $moneyhist_model->getAccList($arrReqData);
			if(array_key_exists($sToday, $arrDateAcc)){
				$arrAcc[0] = $arrDateAcc[$sToday];
			}
			
			$arrReqData['start'] = date('Y-m')."-1";
			$tmStart = strtotime($arrReqData['start']);
			$arrReqData['end'] = date('Y-m-d', strtotime("+1 month", $tmStart));
			$arrAcc[1] = $moneyhist_model->getAccRange($arrReqData);


			$result->data = $arrAcc;
			$result->status = STATUS_SUCCESS;
        }
		
		echo json_encode($result);

    }


	public function pbacclist()
	{
		$jsonData = $_REQUEST['json_'];
		$arrReqData = json_decode($jsonData, true);
		
		$result = new \StdClass;
		if(!is_login())
		{
            $result->status = STATUS_LOGOUT;		
        } else {
			$uid = $this->session->uid;
			$objMember = $this->member_model->getByUid($uid);
			$moneyhist_model = new MoneyHist_Model();
			
			if( !$this->member_model->permittedMember($objMember) ){
				$result->status = STATUS_FAIL;
			} else {
				if($objMember->mb_level == LEVEL_AGENCY) {
					$arrReqData['mb_emp_fid'] = $objMember->mb_fid;
				} else if (!empty($arrReqData['mb_uid'])) {
					$objSelMember = $this->member_model->getByUid($arrReqData['mb_uid']);
					if (!is_null($objSelMember)) {
						if ((int)$objSelMember->mb_level === LEVEL_AGENCY) {
							$arrReqData['mb_uid'] = '';
							$arrReqData['mb_emp_fid'] = $objSelMember->mb_fid;
						} else {
							// 매장 선택: bets.mb_uid 필터
							$arrReqData['mb_uid'] = $objSelMember->mb_uid;
							unset($arrReqData['mb_emp_fid']);
						}
					}
				}
				$arrAcc = $moneyhist_model->getAccList($arrReqData);
				
				$result->data = $arrAcc;
				$result->status = STATUS_SUCCESS;
			}
        }
		
		echo json_encode($result);

    }


	public function member_list()
	{
		$jsonData = $_REQUEST['json_'];
		$arrReqData = json_decode($jsonData, true);

		$result = new \StdClass;
		if(!is_login())
		{
            $result->status = STATUS_LOGOUT;		
        } else {
			

			$uid = $this->session->uid;
			$objMember = $this->member_model->getByUid($uid);
			if( !$this->member_model->permittedMember($objMember) ){
				$result->status = STATUS_FAIL;
			} else {
				$level = LEVEL_EMPLOYEE;
				if($objMember->mb_level > LEVEL_AGENCY){
					$level = LEVEL_AGENCY;
				} else {
					$arrReqData['mb_emp_fid'] = $objMember->mb_fid;
				}

				$arrMember = $this->member_model->searchList($arrReqData, $level);
				$result->data = $arrMember;
				$result->status = STATUS_SUCCESS;
				
			} 
			
        }
		
		echo json_encode($result);
	}


	public function member_betlist()
	{
		
		$result = new \StdClass;
		if(!is_login())
		{
            $result->status = STATUS_LOGOUT;		
        } else {
			

			$uid = $this->session->uid;
			$objMember = $this->member_model->getByUid($uid);
			$arrRoundInfo = getPbRoundInfo();

			if( !$this->member_model->permittedMember($objMember) ){
				$result->status = STATUS_FAIL;
			} else {
				$pbbet_model = new PbBet_Model();
				$arrRoundInfo['mb_emp_fid'] = $objMember->mb_fid;

				$arrMember = $pbbet_model->getMemberByRound($arrRoundInfo);
				$result->data = $arrMember;
				$result->status = STATUS_SUCCESS;
				
			} 
			
        }
		
		echo json_encode($result);
	}
	
	public function member_permit()
	{
		$jsonData = $_REQUEST['json_'];
		$arrReqData = json_decode($jsonData, true);

		$result = new \StdClass;
		if(!is_login())
		{
            $result->status = STATUS_LOGOUT;		
        } else {
			

			$uid = $this->session->uid;
			$objAdmin = $this->member_model->getByUid($uid);
			
			$objMember = $this->member_model->getByFid($arrReqData['mb_fid']);
			
			if( !$this->member_model->permittedMember($objAdmin) ){
				$result->status = STATUS_FAIL;
			} else if( is_null($objMember) ){
				$result->status = STATUS_FAIL;
			} else if($objMember->mb_level == LEVEL_EMPLOYEE && $objMember->mb_emp_fid !== $objAdmin->mb_fid)
			{
				$result->status = STATUS_FAIL;
			} else {
				$bResult = $this->member_model->updatePermit($objMember->mb_uid, $arrReqData['permit']);
				$result->status = STATUS_SUCCESS;
				
			} 
			
        }
		
		echo json_encode($result);
	}

	public function member_rest()
	{
		$jsonData = $_REQUEST['json_'];
		$arrReqData = json_decode($jsonData, true);

		$result = new \StdClass;
		if(!is_login())
		{
            $result->status = STATUS_LOGOUT;		
        } else {
			

			$uid = $this->session->uid;
			$objAdmin = $this->member_model->getByUid($uid);
			
			$objMember = $this->member_model->getByFid($arrReqData['mb_fid']);
			
			if( !$this->member_model->permittedMember($objAdmin) ){
				$result->status = STATUS_FAIL;
			} else if( is_null($objMember) ){
				$result->status = STATUS_FAIL;
			} else if($objMember->mb_level == LEVEL_EMPLOYEE && $objMember->mb_emp_fid !== $objAdmin->mb_fid)
			{
				$result->status = STATUS_FAIL;
			} else {
				$bResult = $this->member_model->updateRest($objMember->mb_uid, $arrReqData['rest']);
				$result->status = STATUS_SUCCESS;
				
			} 
			
        }
		
		echo json_encode($result);
	}

	public function member_delete()
	{
		$jsonData = $_REQUEST['json_'];
		$arrReqData = json_decode($jsonData, true);

		$result = new \StdClass;
		if(!is_login())
		{
            $result->status = STATUS_LOGOUT;		
        } else {
			

			$uid = $this->session->uid;
			$objAdmin = $this->member_model->getByUid($uid);
			
			$objMember = $this->member_model->getByFid($arrReqData['mb_fid']);
			
			if( !$this->member_model->permittedMember($objAdmin) ){
				$result->status = STATUS_FAIL;
			} else if( is_null($objMember) ){
				$result->status = STATUS_FAIL;
			} else if($objMember->mb_level == LEVEL_EMPLOYEE && $objMember->mb_emp_fid !== $objAdmin->mb_fid)
			{
				$result->status = STATUS_FAIL;
			} else {
				if($objMember->mb_level == LEVEL_AGENCY){			//하부매장 삭제
					$this->member_model->deleteByEmpFid($objMember->mb_fid);
				}

				$bResult = $this->member_model->deleteByUid($objMember->mb_uid);
				$result->status = STATUS_SUCCESS;
				
			} 
			
        }
		
		echo json_encode($result);
	}


	public function member_register()
	{
		$jsonData = $_REQUEST['json_'];
		$arrReqData = json_decode($jsonData, true);

		$result = new \StdClass;
		if(!is_login())
		{
            $result->status = STATUS_LOGOUT;		
        } else {
			

			$uid = $this->session->uid;
			$objAdmin = $this->member_model->getByUid($uid);
			
			if( !$this->member_model->permittedMember($objAdmin) ){
				$result->status = STATUS_FAIL;
				$result->code = RESULT_FAIL;
			} else {
				$iResult = $this->member_model->register($arrReqData, $objAdmin);
				
				if($iResult == RESULT_OK){
					$result->status = STATUS_SUCCESS;
				} else {
					$result->status = STATUS_FAIL;
					$result->code = $iResult;	
				}
				
				
			} 
			
        }
		
		echo json_encode($result);
	}


	public function member_fetch()
	{
		$jsonData = $_REQUEST['json_'];
		$arrReqData = json_decode($jsonData, true);

		$result = new \StdClass;
		if(!is_login())
		{
            $result->status = STATUS_LOGOUT;		
        } else {
			

			$uid = $this->session->uid;
			$objAdmin = $this->member_model->getByUid($uid);
			
			$objMember = $this->member_model->getInfoByFid($arrReqData['fid']);

			if( !$this->member_model->permittedMember($objAdmin) ){
				$result->status = STATUS_FAIL;
			} else if( is_null($objMember) ){
				$result->status = STATUS_FAIL;
			} else if($objMember->mb_level == LEVEL_EMPLOYEE && $objMember->mb_emp_fid !== $objAdmin->mb_fid)
			{
				$result->status = STATUS_FAIL;
			} else {
				$result->data = $objMember;
				$result->status = STATUS_SUCCESS;
				
			} 
        }
		
		echo json_encode($result);
	}


	public function member_modify()
	{
		$jsonData = $_REQUEST['json_'];
		$arrReqData = json_decode($jsonData, true);

		$result = new \StdClass;
		if(!is_login())
		{
            $result->status = STATUS_LOGOUT;		
        } else {
			

			$uid = $this->session->uid;
			$objAdmin = $this->member_model->getByUid($uid);
			
			$objMember = $this->member_model->getByUid($arrReqData['uid']);

			if( !$this->member_model->permittedMember($objAdmin) ){
				$result->status = STATUS_FAIL;
			} else if( is_null($objMember) ){
				$result->status = STATUS_FAIL;
			} else if($objMember->mb_level == LEVEL_EMPLOYEE && $objMember->mb_emp_fid !== $objAdmin->mb_fid)
			{
				$result->status = STATUS_FAIL;
			} else {
				
				$iResult = $this->member_model->modify($arrReqData, $objAdmin);
				
				if($iResult == RESULT_OK){
					$result->status = STATUS_SUCCESS;
				} else {
					$result->status = STATUS_FAIL;
					$result->code = $iResult;	
				}
				
			} 
        }
		
		echo json_encode($result);
	}



	public function member_subs()
	{
		$result = new \StdClass;
		if(!is_login())
		{
            $result->status = STATUS_LOGOUT;		
        } else {
			

			$uid = $this->session->uid;
			$objAdmin = $this->member_model->getByUid($uid);
			
			if( !$this->member_model->permittedMember($objAdmin) ){
				$result->status = STATUS_FAIL;
			} else {
				$arrMember = $this->member_model->getSubs($objAdmin);
				
				$result->data = $arrMember;
				$result->status = STATUS_SUCCESS;
				
			} 
        }
		
		echo json_encode($result);
	}



	public function member_pwd()
	{
		$jsonData = $_REQUEST['json_'];
		$arrReqData = json_decode($jsonData, true);

		$result = new \StdClass;
		if(!is_login())
		{
            $result->status = STATUS_LOGOUT;		
        } else {
			
			$uid = $this->session->uid;
			$objAdmin = $this->member_model->getByUid($uid);
			
			if( !$this->member_model->permittedMember($objAdmin) ){
				$result->code = RESULT_ERROR;
				$result->status = STATUS_FAIL;
			} else if(is_null($this->member_model->login($uid, $arrReqData['pwd_cur'])))
			{
				$result->code = RESULT_FAIL;
				$result->status = STATUS_FAIL;
			} else {
				
				$bResult = $this->member_model->updatePwd($objAdmin->mb_uid, $arrReqData['pwd_new']);
				
				$result->status = STATUS_SUCCESS;
			} 
        }
		
		echo json_encode($result);
	}


	public function member_bank()
	{
		$jsonData = $_REQUEST['json_'];
		$arrReqData = json_decode($jsonData, true);

		$result = new \StdClass;
		if(!is_login())
		{
            $result->status = STATUS_LOGOUT;		
        } else {
			
			$uid = $this->session->uid;
			$objAdmin = $this->member_model->getByUid($uid);
			
			if( !$this->member_model->permittedMember($objAdmin) ){
				$result->code = RESULT_ERROR;
				$result->status = STATUS_FAIL;
			}  else {
				
				$bResult = $this->member_model->updateBank($objAdmin->mb_uid, $arrReqData);
				
				$result->status = STATUS_SUCCESS;
			} 
        }
		
		echo json_encode($result);
	}

	
	public function submember_fetch()
	{
		$jsonData = $_REQUEST['json_'];
		$arrReqData = json_decode($jsonData, true);

		$result = new \StdClass;
		if(!is_login())
		{
            $result->status = STATUS_LOGOUT;		
        } else {
			
			$uid = $this->session->uid;
			$objAdmin = $this->member_model->getByUid($uid);
			
			$objMember = $this->member_model->getByFid($arrReqData['fid']);

			if(!$this->member_model->isSubMember($objAdmin, $objMember->mb_uid)){
				$result->status = STATUS_FAIL;
			}
			else if( !$this->member_model->permittedMember($objMember) ){
				$result->status = STATUS_FAIL;
			} else {
				$arrMember = $this->member_model->getSubs($objMember);
				
				$result->data = $arrMember;
				$result->info = $objMember;
				$result->status = STATUS_SUCCESS;
				
			} 
        }
		
		echo json_encode($result);
	}



	public function pbround_count()
	{
		$jsonData = $_REQUEST['json_'];
		$arrReqData = json_decode($jsonData, true);

		$result = new \StdClass;
		if(!is_login())
		{
            $result->status = STATUS_LOGOUT;		
        } else {
			$pbround_model = new PbRound_Model();
			$pbround_model->setType($arrReqData['game']);
			$count = $pbround_model->searchCount($arrReqData);
			
			$result->data = $count;
			$result->status = STATUS_SUCCESS;
        }
		
		echo json_encode($result);

    }

	public function pbround_page()
	{
		$jsonData = $_REQUEST['json_'];
		$arrReqData = json_decode($jsonData, true);

		$result = new \StdClass;
		if(!is_login())
		{
            $result->status = STATUS_LOGOUT;		
        } else {
			
			$uid = $this->session->uid;
			$objMember = $this->member_model->getAllByUid($uid);
			if($objMember->mb_level == LEVEL_AGENCY){
				$arrReqData['mb_emp_fid'] = $objMember->mb_fid; 
			}

			$pbround_model = new PbRound_Model();
			$pbround_model->setType($arrReqData['game']);
			$arrRound = $pbround_model->searchList($arrReqData, $arrReqData['page'], $arrReqData['cntper']);

			$result->data = $arrRound;
			$result->game = intval($arrReqData['game']);
			$result->status = STATUS_SUCCESS;
        }
		
		echo json_encode($result);

    }

	public function pbbetlist_count()
	{
		$jsonData = $_REQUEST['json_'];
		$arrReqData = json_decode($jsonData, true);

		$result = new \StdClass;
		if(!is_login())
		{
            $result->status = STATUS_LOGOUT;		
        } else {
			
			$uid = $this->session->uid;
			$objMember = $this->member_model->getAllByUid($uid);
			if($objMember->mb_level == LEVEL_AGENCY){
				$arrReqData['mb_emp_fid'] = $objMember->mb_fid; 
			}

            $pbbet_model = new PbBet_Model();
			$nCount = $pbbet_model->searchCount($arrReqData);

			$result->data = $nCount;
			$result->status = STATUS_SUCCESS;
        }
		
		echo json_encode($result);

    }

	public function pbbetlist_page()
	{
		$jsonData = $_REQUEST['json_'];
		$arrReqData = json_decode($jsonData, true);

		$result = new \StdClass;
		if(!is_login())
		{
            $result->status = STATUS_LOGOUT;		
        } else {
			$uid = $this->session->uid;
			$objMember = $this->member_model->getAllByUid($uid);
			if($objMember->mb_level == LEVEL_AGENCY){
				$arrReqData['mb_emp_fid'] = $objMember->mb_fid; 
			}
			
            $pbbet_model = new PbBet_Model();
			$arrBet = $pbbet_model->searchList($arrReqData, $arrReqData['page'], $arrReqData['cntper']);

			$result->data = $arrBet;
			$result->level = $objMember->mb_level;
			$result->status = STATUS_SUCCESS;
        }
		
		echo json_encode($result);

    }

	public function pbbetlist_last()
	{
		$jsonData = $_REQUEST['json_'];
		$arrReqData = json_decode($jsonData, true);

		$result = new \StdClass;
		if(!is_login())
		{
            $result->status = STATUS_LOGOUT;		
        } else {
			$uid = $this->session->uid;
			$objMember = $this->member_model->getAllByUid($uid);

			if($objMember->mb_level >= LEVEL_ADMIN){
				$pbbet_model = new PbBet_Model();
				$roundInfo = getPbLastRoundInfo();
				$arrReqData['start'] = $roundInfo['round_date'];
				$arrReqData['end'] = "";
				$arrReqData['round_id'] = $roundInfo['round_no'];
				$arrReqData['mode'] = 1;

				$arrBet = $pbbet_model->searchList($arrReqData, 1, 100);
	
				$result->data = $arrBet;
				$result->status = STATUS_SUCCESS;	
			}
			else 			
				$result->status = STATUS_FAIL;	
        }
		
		echo json_encode($result);

    }
	
	public function pbacc_round()
	{
		$jsonData = $_REQUEST['json_'];
		$arrReqData = json_decode($jsonData, true);

		$result = new \StdClass;
		if(!is_login())
		{
            $result->status = STATUS_LOGOUT;		
        } else {
			$uid = $this->session->uid;
			$objMember = $this->member_model->getAllByUid($uid);
			if($objMember->mb_level == LEVEL_AGENCY){
				$arrReqData['mb_emp_fid'] = $objMember->mb_fid; 
			}

			$pbbet_model = new PbBet_Model();

			$arrAcc = $pbbet_model->getAccByRound($arrReqData);

			$result->data = $arrAcc;
			$result->game = intval($arrReqData['game']);
			$result->status = STATUS_SUCCESS;
        }
		
		echo json_encode($result);

    }

	/** 배팅내역 — 하부 매장별 합계 (포인트 = mb_point) */
	public function store_bet_summary()
	{
		$jsonData = $_REQUEST['json_'];
		$arrReqData = json_decode($jsonData, true);

		$result = new \StdClass;
		if (!is_login()) {
			$result->status = STATUS_LOGOUT;
		} else {
			$uid = $this->session->uid;
			$objMember = $this->member_model->getAllByUid($uid);
			if ($objMember->mb_level == LEVEL_AGENCY) {
				$arrReqData['mb_emp_fid'] = $objMember->mb_fid;
			} else if ($objMember->mb_level == LEVEL_EMPLOYEE) {
				$arrReqData['mb_uid'] = $objMember->mb_uid;
			}
			$pbbet_model = new PbBet_Model();
			$result->data = $pbbet_model->getStoreBetSummary($arrReqData);
			$result->status = STATUS_SUCCESS;
		}
		echo json_encode($result);
	}

	public function edit_bet(){
		$jsonData = $_REQUEST['json_'];
		$arrReqData = json_decode($jsonData, true);

		$result = new \StdClass;
		if(!is_login())
		{
            $result->status = STATUS_LOGOUT;		
        } else {
			$pbbet_model = new PbBet_Model();
			$confgame_model = new ConfGame_Model();

			$uid = $this->session->uid;
			$objMember = $this->member_model->getByUid($uid);
			
			$objBet = $pbbet_model->getByFid($arrReqData['id']);
			$objConf = $confgame_model->getById($arrReqData['game']);

			if($objMember->mb_level < LEVEL_ADMIN){
				$result->status = STATUS_FAIL;		
			} else if(is_null($objBet) || $objBet->bet_state != BET_WAIT) {
				$result->status = STATUS_FAIL;		
			} else if(setBetRatio($arrReqData, $objConf) === false){
				$result->status = STATUS_FAIL;		
			} else {
				// $pbbet_model->updateBet($arrReqData);
				$result->status = STATUS_SUCCESS;		
			}
		}
		echo json_encode($result);

	}

	public function chg_bet(){
		$jsonData = $_REQUEST['json_'];
		$arrReqData = json_decode($jsonData, true);

		$result = new \StdClass;
		if(!is_login())
		{
            $result->status = STATUS_LOGOUT;		
        } else {
			$pbbet_model = new PbBet_Model();
			$confgame_model = new ConfGame_Model();
			$moneyhist_model = new MoneyHist_Model();

			$uid = $this->session->uid;
			$objAdmin = $this->member_model->getByUid($uid);
			
			$objBet = $pbbet_model->getByFid($arrReqData['id']);
			$objConf = $confgame_model->getById($arrReqData['game']);
			$lastRound = getPbLastRoundInfo();

			if($objAdmin->mb_level < LEVEL_ADMIN){
				$result->status = STATUS_FAIL;		
			} else if(is_null($objBet) || $objBet->bet_state != BET_LOSS) {
				$result->status = STATUS_FAIL;		
			} else if(setChgRatio($arrReqData, $objConf) === false){
				$result->status = STATUS_FAIL;		
			} else if($objBet->bet_round_date != $lastRound['round_date'] || 
				$objBet->bet_round_no != $lastRound['round_no']){
				$result->msg = "변경기간이 아닙니다.";		
				$result->status = STATUS_FAIL;		
			} else {
				$objMember = $this->member_model->getByUid($objBet->bet_mb_uid);
				$objBet->bet_mode = $arrReqData['mode'];
				$objBet->bet_target = $arrReqData['target'];
				$objBet->bet_ratio = $arrReqData['ratio'];
				$objBet->bet_state = BET_WIN;
				//bet_win_money
				$nWinMoney = intval($objBet->bet_money) * floatval($objBet->bet_ratio);
				$objBet->bet_win_money = round($nWinMoney);
				//user_after_money
				$objBet->bet_after_money = $objMember->mb_money + $objBet->bet_win_money;
            
				if($this->member_model->updateAssets($objMember->mb_fid, $objBet->bet_win_money) ){
					$pbbet_model->updateBetObj($objBet);
					$moneyhist_model->registerMoneyAcc($objMember, $objBet);
				}
				$result->status = STATUS_SUCCESS;		
			}
		}
		echo json_encode($result);

	}


	//----------------------  Charge  ------------------------------
	public function charge_req()
	{
		$jsonData = $_REQUEST['json_'];
		$arrReqData = json_decode($jsonData, true);

		$result = new \StdClass;
		if(!is_login())
		{
            $result->status = STATUS_LOGOUT;		
        } else {
			$uid = $this->session->uid;
			$objMember = $this->member_model->getByUid($uid);

			$charge_model = new Charge_Model();

			/*if($this->confsite_model->IsMaintain()){
				$result->code = RESULT_MAINTAIN;
				$result->status = STATUS_FAIL;
			}  else*/ if(!$this->member_model->permittedMember($objMember) || $objMember->mb_level != LEVEL_AGENCY){
				$result->code = RESULT_FAIL;
				$result->status = STATUS_FAIL;
			} else if(!is_null($charge_model->waitCharge($uid)) ){
				$result->code = RESULT_STOP;
				$result->status = STATUS_FAIL;
			} else {
				
				$bResult = $charge_model->addCharge($objMember, $arrReqData);
				$result->status = STATUS_SUCCESS;
			}			
        }
		
		echo json_encode($result);

    }


	public function charge_count()
	{
		$result = new \StdClass;
		if(!is_login())
		{
            $result->status = STATUS_LOGOUT;		
        } else {
			$arrReqData['mb_uid'] = $this->session->uid;

			$charge_model = new Charge_Model();
			$count = $charge_model->searchCount($arrReqData);

			$result->data = $count;
			$result->status = STATUS_SUCCESS;
        }
		
		echo json_encode($result);

    }


	public function charge_page()
	{
		$jsonData = $_REQUEST['json_'];
		$arrReqData = json_decode($jsonData, true);

		$result = new \StdClass;
		if(!is_login())
		{
            $result->status = STATUS_LOGOUT;		
        } else {
			$arrReqData['mb_uid'] = $this->session->uid;

			$charge_model = new Charge_Model();
			$arrCharge = $charge_model->searchList($arrReqData, $arrReqData['page'], $arrReqData['cntper']);

			$result->data = $arrCharge;
			$result->status = STATUS_SUCCESS;
        }
		
		echo json_encode($result);

    }
	
	public function charge_delete()
	{
		$jsonData = $_REQUEST['json_'];
		$arrReqData = json_decode($jsonData, true);

		$result = new \StdClass;
		if(!is_login())
		{
            $result->status = STATUS_LOGOUT;		
        } else {
			$uid = $this->session->uid;

			$charge_model = new Charge_Model();

			$objCharge = $charge_model->getById($arrReqData['charge_id']);
			
			if(is_null($objCharge) || $objCharge->charge_action_state == CHARGE_STATE_WAIT){
				$result->status = STATUS_FAIL;
				
			} else {
				$bResult = $charge_model->deleteCharge($objCharge->charge_fid);
				$result->status = STATUS_SUCCESS;
			}			
        }
		
		echo json_encode($result);

    }



	
	public function chargeproc_count()
	{
		$jsonData = $_REQUEST['json_'];
		$arrReqData = json_decode($jsonData, true);

		$result = new \StdClass;
		if(!is_login())
		{
            $result->status = STATUS_LOGOUT;		
        } else {
			$uid = $this->session->uid;
			$objMember = $this->member_model->getAllByUid($uid);

			$arrReqData['mb_emp_fid'] = $objMember->mb_level == LEVEL_AGENCY ? $objMember->mb_fid : 0;
			$arrReqData['mb_emp_uid'] = $objMember->mb_level > LEVEL_AGENCY ? $objMember->mb_uid : "";

			$charge_model = new Charge_Model();
			$count = $charge_model->searchProcCount($arrReqData);

			$result->data = $count;
			$result->status = STATUS_SUCCESS;
        }
		
		echo json_encode($result);

    }


	public function chargeproc_page()
	{
		$jsonData = $_REQUEST['json_'];
		$arrReqData = json_decode($jsonData, true);

		$result = new \StdClass;
		if(!is_login())
		{
            $result->status = STATUS_LOGOUT;		
        } else {
			$uid = $this->session->uid;
			$objMember = $this->member_model->getAllByUid($uid);

			$arrReqData['mb_emp_fid'] = $objMember->mb_level == LEVEL_AGENCY ? $objMember->mb_fid : 0;
			$arrReqData['mb_emp_uid'] = $objMember->mb_level > LEVEL_AGENCY ? $objMember->mb_uid : "";

			$charge_model = new Charge_Model();
			$arrCharge = $charge_model->searchProcList($arrReqData, $arrReqData['page'], $arrReqData['cntper']);

			$result->data = $arrCharge;
			$result->status = STATUS_SUCCESS;
        }
		
		echo json_encode($result);

    }
	
	public function chargeproc_delete()
	{
		$jsonData = $_REQUEST['json_'];
		$arrReqData = json_decode($jsonData, true);

		$result = new \StdClass;
		if(!is_login())
		{
            $result->status = STATUS_LOGOUT;		
        } else {
			$uid = $this->session->uid;

			$charge_model = new Charge_Model();

			$objCharge = $charge_model->getById($arrReqData['charge_id']);
			
			if(is_null($objCharge) || $objCharge->charge_action_state == CHARGE_STATE_WAIT){
				$result->status = STATUS_FAIL;
				
			} else {
				$bResult = $charge_model->deleteChargeProc($objCharge->charge_fid);
				$result->status = STATUS_SUCCESS;
			}			
        }
		
		echo json_encode($result);

    }

		
	public function chargeproc_permit()
	{
		$jsonData = $_REQUEST['json_'];
		$arrReqData = json_decode($jsonData, true);

		$result = new \StdClass;
		if(!is_login())
		{
            $result->status = STATUS_LOGOUT;		
        } else {
			$uid = $this->session->uid;
			$objAdmin = $this->member_model->getByUid($uid);

			$charge_model = new Charge_Model();
			$moneyhist_model = new MoneyHist_Model();

			$objCharge = $charge_model->getById($arrReqData['charge_id']);
			
			$objMember = null;
			if(!is_null($objCharge)) {
				$objMember = $this->member_model->getByUid($objCharge->charge_mb_uid);
			}

			if(is_null($objMember) || $objCharge->charge_action_state != CHARGE_STATE_WAIT){
				$result->status = STATUS_FAIL;
				$result->code = RESULT_FAIL;
			} else if($objAdmin->mb_level == LEVEL_AGENCY && $objAdmin->mb_fid !== $objCharge->charge_emp_fid){
				$result->status = STATUS_FAIL;
				$result->code = RESULT_FAIL;
			} else if($objAdmin->mb_level == LEVEL_AGENCY && intval($objAdmin->mb_money) < intval($objCharge->charge_money)){
				$result->code = RESULT_OVERMONEY;		
				$result->status = STATUS_FAIL;
			} else {

				if($objAdmin->mb_level == LEVEL_AGENCY){
					$objAdmin->mb_ech_uid = $objMember->mb_uid;
					$this->member_model->updateAssets($objAdmin->mb_fid, 0-$objCharge->charge_money);
					$moneyhist_model->registerChargeFrom($objAdmin, $objCharge->charge_money);
				
					$objMember->mb_ech_uid = $objAdmin->mb_uid;
				} else {
					$objMember->mb_ech_uid = SITE_MASTER_NAME;
				}
				
				$this->member_model->updateAssets($objMember->mb_fid, $objCharge->charge_money);
				$moneyhist_model->registerCharge($objMember, $objCharge->charge_money);
				

				$bResult = $charge_model->procCharge($objCharge, $objAdmin, CHARGE_STATE_PERMIT);
				$result->status = STATUS_SUCCESS;
			}			
        }
		
		echo json_encode($result);

    }

		
	public function chargeproc_cancel()
	{
		$jsonData = $_REQUEST['json_'];
		$arrReqData = json_decode($jsonData, true);

		$result = new \StdClass;
		if(!is_login())
		{
            $result->status = STATUS_LOGOUT;		
        } else {
			$uid = $this->session->uid;
			$objAdmin = $this->member_model->getAllByUid($uid);

			$charge_model = new Charge_Model();

			$objCharge = $charge_model->getById($arrReqData['charge_id']);			

			if(is_null($objCharge) || $objCharge->charge_action_state != CHARGE_STATE_WAIT){
				$result->status = STATUS_FAIL;
				$result->code = RESULT_FAIL;
			} else if($objAdmin->mb_level == LEVEL_AGENCY && $objAdmin->mb_fid !== $objCharge->charge_emp_fid){
				$result->status = STATUS_FAIL;
				$result->code = RESULT_FAIL;
			} else {

				$bResult = $charge_model->procCharge($objCharge, $objAdmin, CHARGE_STATE_REFUSE);
				$result->status = STATUS_SUCCESS;
			}				
        }
		
		echo json_encode($result);

    }


			
	public function charge_service()
	{
		$jsonData = $_REQUEST['json_'];
		$arrReqData = json_decode($jsonData, true);

		$result = new \StdClass;
		if(!is_login())
		{
            $result->status = STATUS_LOGOUT;		
        } else {
			$uid = $this->session->uid;
			$objAdmin = $this->member_model->getByUid($uid);

			$charge_model = new Charge_Model();
			$moneyhist_model = new MoneyHist_Model();
			
			$objMember = $this->member_model->getByUid($arrReqData['uid']);
			

			if(!$this->member_model->permittedMember($objAdmin) || is_null($objMember)){
				$result->status = STATUS_FAIL;
				$result->code = RESULT_FAIL;
			} else if($objAdmin->mb_level == LEVEL_AGENCY && $objAdmin->mb_fid !== $objMember->mb_emp_fid){
				$result->status = STATUS_FAIL;
				$result->code = RESULT_FAIL;
			} else if($objAdmin->mb_level == LEVEL_AGENCY && intval($objAdmin->mb_money) < intval($arrReqData['money'])){
				$result->code = RESULT_OVERMONEY;		
				$result->status = STATUS_FAIL;
			} else {

				if($objAdmin->mb_level == LEVEL_AGENCY){
					$objAdmin->mb_ech_uid = $objMember->mb_uid;
					$this->member_model->updateAssets($objAdmin->mb_fid, 0-intval($arrReqData['money']));
					$moneyhist_model->registerPresentFrom($objAdmin, intval($arrReqData['money']));
				
					$objMember->mb_ech_uid = $objAdmin->mb_uid;
				} else {
					$objMember->mb_ech_uid = SITE_MASTER_NAME;
				}
				
				$this->member_model->updateAssets($objMember->mb_fid, intval($arrReqData['money']));
				$moneyhist_model->registerPresent($objMember, intval($arrReqData['money']));
				
				$charge_model->addPresent($objMember, $objAdmin, $arrReqData);

				$result->status = STATUS_SUCCESS;
			}			
        }
		
		echo json_encode($result);

    }

	//---------------------  Exchange  --------------------------
	public function exchange_req()
	{
		$jsonData = $_REQUEST['json_'];
		$arrReqData = json_decode($jsonData, true);

		$result = new \StdClass;
		if(!is_login())
		{
            $result->status = STATUS_LOGOUT;		
        } else {
			$uid = $this->session->uid;
			$objMember = $this->member_model->getByUid($uid);
			
			$exchange_model = new Exchange_Model();
			$moneyhist_model = new MoneyHist_Model();

			if(!$this->member_model->permittedMember($objMember) || $objMember->mb_level != LEVEL_AGENCY){
				$result->code = RESULT_FAIL;
				$result->status = STATUS_FAIL;
			} else if(is_null($this->member_model->login_bank($uid, $arrReqData['bank_pwd'])) ){
				$result->code = RESULT_ERROR;		
				$result->status = STATUS_FAIL;
			} else if($objMember->mb_money < intval($arrReqData['amount']) ){
				$result->code = RESULT_OVERMONEY;		
				$result->status = STATUS_FAIL;
			}
			/*else if(!is_null($exchange_model->waitExchange($uid)) ){
				$result->code = RESULT_STOP;
				$result->status = STATUS_FAIL;
			}*/ else {
				$objMember->mb_ech_uid = SITE_MASTER_NAME;
				
				$this->member_model->updateAssets($objMember->mb_fid, 0-$arrReqData['amount']);
				$moneyhist_model->registerExchange($objMember, $arrReqData['amount']);
				
				$arrReqData['exchange_money_after'] = $objMember->mb_money -  $arrReqData['amount'];
				$bResult = $exchange_model->addExChange($objMember, $arrReqData);
				
				$result->status = STATUS_SUCCESS;
			}			
        }
		
		echo json_encode($result);

    }


	public function exchange_count()
	{
		$result = new \StdClass;
		if(!is_login())
		{
            $result->status = STATUS_LOGOUT;		
        } else {
			$arrReqData['mb_uid'] = $this->session->uid;

			$exchange_model = new Exchange_Model();
			$count = $exchange_model->searchCount($arrReqData);

			$result->data = $count;
			$result->status = STATUS_SUCCESS;
        }
		
		echo json_encode($result);

    }


	public function exchange_page()
	{
		$jsonData = $_REQUEST['json_'];
		$arrReqData = json_decode($jsonData, true);

		$result = new \StdClass;
		if(!is_login())
		{
            $result->status = STATUS_LOGOUT;		
        } else {
			$arrReqData['mb_uid'] = $this->session->uid;

			$exchange_model = new Exchange_Model();
			$arrExchange = $exchange_model->searchList($arrReqData, $arrReqData['page'], $arrReqData['cntper']);

			$result->data = $arrExchange;
			$result->status = STATUS_SUCCESS;
        }
		
		echo json_encode($result);

    }
	
	public function exchange_delete()
	{
		$jsonData = $_REQUEST['json_'];
		$arrReqData = json_decode($jsonData, true);

		$result = new \StdClass;
		if(!is_login())
		{
            $result->status = STATUS_LOGOUT;		
        } else {
			$uid = $this->session->uid;

			$exchange_model = new ExChange_Model();

			$objExchange = $exchange_model->getById($arrReqData['exchange_id']);
			
			if(is_null($objExchange) || $objExchange->exchange_action_state == CHARGE_STATE_WAIT){
				$result->status = STATUS_FAIL;
				
			} else {
				$bResult = $exchange_model->deleteExChange($objExchange->exchange_fid);
				$result->status = STATUS_SUCCESS;
			}			
        }
		
		echo json_encode($result);

    }


	public function exchangeproc_count()
	{
		$jsonData = $_REQUEST['json_'];
		$arrReqData = json_decode($jsonData, true);

		$result = new \StdClass;
		if(!is_login())
		{
            $result->status = STATUS_LOGOUT;		
        } else {
			$uid = $this->session->uid;
			$objMember = $this->member_model->getAllByUid($uid);

			$arrReqData['mb_emp_fid'] = $objMember->mb_level == LEVEL_AGENCY ? $objMember->mb_fid : 0;
			$arrReqData['mb_emp_uid'] = $objMember->mb_level > LEVEL_AGENCY ? $objMember->mb_uid : "";
			
			$exchange_model = new Exchange_Model();
			$count = $exchange_model->searchProcCount($arrReqData);

			$result->data = $count;
			$result->status = STATUS_SUCCESS;
        }
		
		echo json_encode($result);

    }


	public function exchangeproc_page()
	{
		$jsonData = $_REQUEST['json_'];
		$arrReqData = json_decode($jsonData, true);

		$result = new \StdClass;
		if(!is_login())
		{
            $result->status = STATUS_LOGOUT;		
        } else {
			$uid = $this->session->uid;
			$objMember = $this->member_model->getAllByUid($uid);

			$arrReqData['mb_emp_fid'] = $objMember->mb_level == LEVEL_AGENCY ? $objMember->mb_fid : 0;
			$arrReqData['mb_emp_uid'] = $objMember->mb_level > LEVEL_AGENCY ? $objMember->mb_uid : "";
			
			$exchange_model = new Exchange_Model();
			$arrExchange = $exchange_model->searchProcList($arrReqData, $arrReqData['page'], $arrReqData['cntper']);

			$result->data = $arrExchange;
			$result->status = STATUS_SUCCESS;
        }
		
		echo json_encode($result);

    }
	
	public function exchangeproc_delete()
	{
		$jsonData = $_REQUEST['json_'];
		$arrReqData = json_decode($jsonData, true);

		$result = new \StdClass;
		if(!is_login())
		{
            $result->status = STATUS_LOGOUT;		
        } else {
			$uid = $this->session->uid;

			$exchange_model = new Exchange_Model();

			$objExChange = $exchange_model->getById($arrReqData['exchange_id']);
			
			if(is_null($objExChange) || $objExChange->exchange_action_state == CHARGE_STATE_WAIT){
				$result->status = STATUS_FAIL;
				
			} else {
				$bResult = $exchange_model->deleteExchangeProc($objExChange->exchange_fid);
				$result->status = STATUS_SUCCESS;
			}			
        }
		
		echo json_encode($result);

    }

		
	public function exchangeproc_permit()
	{
		$jsonData = $_REQUEST['json_'];
		$arrReqData = json_decode($jsonData, true);

		$result = new \StdClass;
		if(!is_login())
		{
            $result->status = STATUS_LOGOUT;		
        } else {
			$uid = $this->session->uid;
			$objAdmin = $this->member_model->getAllByUid($uid);

			$exchange_model = new Exchange_Model();
			$moneyhist_model = new MoneyHist_Model();

			$objExchange = $exchange_model->getById($arrReqData['exchange_id']);
			
			if(is_null($objExchange) || $objExchange->exchange_action_state != CHARGE_STATE_WAIT){
				$result->status = STATUS_FAIL;
				$result->code = RESULT_ERROR;
			} else if($objAdmin->mb_level == LEVEL_AGENCY && $objAdmin->mb_fid !== $objExchange->exchange_emp_fid){
				$result->status = STATUS_FAIL;
				$result->code = RESULT_FAIL;
			} else {
				
				$bResult = $exchange_model->procExchange($objExchange, $objAdmin, CHARGE_STATE_PERMIT);
				$result->status = STATUS_SUCCESS;
			}			
        }
		
		echo json_encode($result);

    }



	public function exchange_service()
	{
		$jsonData = $_REQUEST['json_'];
		$arrReqData = json_decode($jsonData, true);

		$result = new \StdClass;
		if(!is_login())
		{
            $result->status = STATUS_LOGOUT;		
        } else {
			$uid = $this->session->uid;
			$objAdmin = $this->member_model->getByUid($uid);

			$objMember = $this->member_model->getByUid($arrReqData['uid']);
			
			$exchange_model = new Exchange_Model();
			$moneyhist_model = new MoneyHist_Model();

			if(!$this->member_model->permittedMember($objAdmin) || is_null($objMember) ){
				$result->code = RESULT_FAIL;
				$result->status = STATUS_FAIL;
			} else if($objMember->mb_money < intval($arrReqData['money'])){
				$result->code = RESULT_OVERMONEY;		
				$result->status = STATUS_FAIL;
			} else if($objAdmin->mb_level == LEVEL_AGENCY && $objAdmin->mb_fid !== $objMember->mb_emp_fid){
				$result->code = RESULT_FAIL;
				$result->status = STATUS_FAIL;
			} else {
				
				if($objAdmin->mb_level == LEVEL_AGENCY){
					$objAdmin->mb_ech_uid = $objMember->mb_uid;
					$this->member_model->updateAssets($objAdmin->mb_fid, intval($arrReqData['money']));
					$moneyhist_model->registerRecoveryTo($objAdmin, intval($arrReqData['money']));
				
					$objMember->mb_ech_uid = $objAdmin->mb_uid;
				} else {
					$objMember->mb_ech_uid = SITE_MASTER_NAME;
				}
				
				$this->member_model->updateAssets($objMember->mb_fid, 0-intval($arrReqData['money']));
				$moneyhist_model->registerRecovery($objMember, 0-intval($arrReqData['money']));

				$exchange_model->addRecovery($objMember, $objAdmin, $arrReqData);

				$result->status = STATUS_SUCCESS;

			}			
        }
		
		echo json_encode($result);

    }


	//---------------------- Memo ----------------------------


	public function memolist_count()
	{
		$jsonData = $_REQUEST['json_'];
		$arrReqData = json_decode($jsonData, true);

		$result = new \StdClass;
		if(!is_login())
		{
            $result->status = STATUS_LOGOUT;		
        } else {
			$arrReqData['send_uid'] = $this->session->uid;

			$count = $this->notice_model->searchCount($arrReqData, NOTICE_TYPE_MSG);

			$result->data = $count;
			$result->status = STATUS_SUCCESS;
        }
		
		echo json_encode($result);

    }


	public function memolist_page()
	{
		$jsonData = $_REQUEST['json_'];
		$arrReqData = json_decode($jsonData, true);

		$result = new \StdClass;
		if(!is_login())
		{
            $result->status = STATUS_LOGOUT;		
        } else {
			$arrReqData['send_uid'] = $this->session->uid;

			$arrNotice = $this->notice_model->searchList($arrReqData, NOTICE_TYPE_MSG, $arrReqData['page'], $arrReqData['cntper']);

			$result->data = $arrNotice;
			$result->status = STATUS_SUCCESS;
        }
		
		echo json_encode($result);

    }


	
	public function memolist_delete()
	{
		$jsonData = $_REQUEST['json_'];
		$arrReqData = json_decode($jsonData, true);

		$result = new \StdClass;
		if(!is_login())
		{
            $result->status = STATUS_LOGOUT;		
        } else {
			$uid = $this->session->uid;
			
			$objNotice = $this->notice_model->getById($arrReqData['no'], NOTICE_TYPE_MSG);
			
			if(is_null($objNotice) || $objNotice->notice_send_uid != $uid){
				$result->status = STATUS_FAIL;
			} else {
				$bResult = $this->notice_model->deleteSendById($arrReqData['no']);

				$result->status = STATUS_SUCCESS;
			}
			
        }
		
		echo json_encode($result);

    }


	public function memolist_reg()
	{
		$jsonData = $_REQUEST['json_'];
		$arrReqData = json_decode($jsonData, true);

		$result = new \StdClass;
		if(!is_login())
		{
            $result->status = STATUS_LOGOUT;		
        } else {
			$uid = $this->session->uid;
			$objAdmin = $this->member_model->getByUid($uid);

			if(!$this->member_model->isSubMember($objAdmin, $arrReqData['recv_uid'])){
				$result->status = STATUS_FAIL;		
			} else {

				$arrReqData['send_uid'] = $uid;

				$bResult = $this->notice_model->registerMemo($arrReqData);
	
				$result->status = STATUS_SUCCESS;
			}

        }
		
		echo json_encode($result);

    }


	public function memolist_mod()
	{
		$jsonData = $_REQUEST['json_'];
		$arrReqData = json_decode($jsonData, true);

		$result = new \StdClass;
		if(!is_login())
		{
            $result->status = STATUS_LOGOUT;		
        } else {
			$arrReqData['send_uid'] = $this->session->uid;
			
			$bResult = $this->notice_model->modifyMemo($arrReqData);

			$result->status = STATUS_SUCCESS;

        }
		
		echo json_encode($result);

    }


	//------------- Qna ------------------

	public function qnalist_count()
	{
		$result = new \StdClass;
		if(!is_login())
		{
            $result->status = STATUS_LOGOUT;		
        } else {
			$arrReqData['recv_uid'] = $this->session->uid;

			$count = $this->notice_model->searchCount($arrReqData, NOTICE_TYPE_QNA);

			$result->data = $count;
			$result->status = STATUS_SUCCESS;
        }
		
		echo json_encode($result);

    }


	public function qnalist_page()
	{
		$jsonData = $_REQUEST['json_'];
		$arrReqData = json_decode($jsonData, true);

		$result = new \StdClass;
		if(!is_login())
		{
            $result->status = STATUS_LOGOUT;		
        } else {
			$arrReqData['recv_uid'] = $this->session->uid;

			$arrNotice = $this->notice_model->searchList($arrReqData, NOTICE_TYPE_QNA, $arrReqData['page'], $arrReqData['cntper']);

			$result->data = $arrNotice;
			$result->status = STATUS_SUCCESS;
        }
		
		echo json_encode($result);

    }


	
	public function qnalist_delete()
	{
		$jsonData = $_REQUEST['json_'];
		$arrReqData = json_decode($jsonData, true);

		$result = new \StdClass;
		if(!is_login())
		{
            $result->status = STATUS_LOGOUT;		
        } else {
			$uid = $this->session->uid;
			
			$objNotice = $this->notice_model->getById($arrReqData['no'], NOTICE_TYPE_QNA);
			
			if(is_null($objNotice) || $objNotice->notice_recv_uid != $uid){
				$result->status = STATUS_FAIL;
			} else {
				$bResult = $this->notice_model->deleteRecvById($arrReqData['no']);

				$result->status = STATUS_SUCCESS;
			}
			
        }
		
		echo json_encode($result);

    }


	public function qnalist_reg()
	{
		$jsonData = $_REQUEST['json_'];
		$arrReqData = json_decode($jsonData, true);

		$result = new \StdClass;
		if(!is_login())
		{
            $result->status = STATUS_LOGOUT;		
        } else {
			$uid = $this->session->uid;
			$objAdmin = $this->member_model->getByUid($uid);

			$objNotice = $this->notice_model->getById($arrReqData['no'], NOTICE_TYPE_QNA);

			if(!$this->member_model->permittedMember($objAdmin) || is_null($objNotice)){
				$result->status = STATUS_FAIL;		
			} else if($objNotice->notice_recv_uid != $objAdmin->mb_uid){
				$result->status = STATUS_FAIL;
			}  else {

				$bResult = $this->notice_model->answerQna($arrReqData);
	
				$result->status = STATUS_SUCCESS;
			}

        }
		
		echo json_encode($result);

    }


	//------------------------------------

	/** 매장 충환전 집계 */
	public function store_ce_summary()
	{
		$jsonData = $_REQUEST['json_'];
		$arrReqData = json_decode($jsonData, true);
		if (!is_array($arrReqData)) {
			$arrReqData = [];
		}

		$result = new \StdClass;
		if (!is_login()) {
			$result->status = STATUS_LOGOUT;
		} else {
			$uid = $this->session->uid;
			$objMember = $this->member_model->getByUid($uid);
			if ($objMember->mb_level == LEVEL_AGENCY) {
				$arrReqData['mb_emp_fid'] = $objMember->mb_fid;
			}
			$moneyhist_model = new MoneyHist_Model();
			$result->data = $moneyhist_model->getCeSummary($arrReqData, 'store');
			$result->status = STATUS_SUCCESS;
		}
		echo json_encode($result);
	}

	/** 총판 충환전 집계 */
	public function agency_ce_summary()
	{
		$jsonData = $_REQUEST['json_'];
		$arrReqData = json_decode($jsonData, true);
		if (!is_array($arrReqData)) {
			$arrReqData = [];
		}

		$result = new \StdClass;
		if (!is_login()) {
			$result->status = STATUS_LOGOUT;
		} else {
			$uid = $this->session->uid;
			$objMember = $this->member_model->getByUid($uid);
			if ($objMember->mb_level == LEVEL_AGENCY) {
				$arrReqData['self_uid'] = $objMember->mb_uid;
			}
			$moneyhist_model = new MoneyHist_Model();
			$result->data = $moneyhist_model->getCeSummary($arrReqData, 'agency');
			$result->status = STATUS_SUCCESS;
		}
		echo json_encode($result);
	}

	/** 충환전 상세 (store|agency) */
	public function ce_detail()
	{
		$jsonData = $_REQUEST['json_'];
		$arrReqData = json_decode($jsonData, true);
		if (!is_array($arrReqData)) {
			$arrReqData = [];
		}

		$result = new \StdClass;
		if (!is_login()) {
			$result->status = STATUS_LOGOUT;
		} else {
			$uid = $this->session->uid;
			$objAdmin = $this->member_model->getByUid($uid);
			$scope = (isset($arrReqData['scope']) && $arrReqData['scope'] === 'agency') ? 'agency' : 'store';
			$targetUid = isset($arrReqData['mb_uid']) ? trim($arrReqData['mb_uid']) : '';

			if ($targetUid === '') {
				$result->status = STATUS_FAIL;
				$result->code = RESULT_FAIL;
			} else {
				$objTarget = $this->member_model->getByUid($targetUid);
				$ok = !is_null($objTarget);
				if ($ok && $objAdmin->mb_level == LEVEL_AGENCY) {
					if ($scope === 'store') {
						$ok = ($objTarget->mb_emp_fid == $objAdmin->mb_fid);
						$arrReqData['mb_emp_fid'] = $objAdmin->mb_fid;
					} else {
						$ok = ($objTarget->mb_uid === $objAdmin->mb_uid);
					}
				}
				if (!$ok) {
					$result->status = STATUS_FAIL;
					$result->code = RESULT_FAIL;
				} else {
					$moneyhist_model = new MoneyHist_Model();
					$result->data = $moneyhist_model->getCeDetail($arrReqData, $scope);
					$result->status = STATUS_SUCCESS;
				}
			}
		}
		echo json_encode($result);
	}

	public function moneylog_count()
	{
		$jsonData = $_REQUEST['json_'];
		$arrReqData = json_decode($jsonData, true);

		$result = new \StdClass;
		if(!is_login())
		{
            $result->status = STATUS_LOGOUT;		
        } else {
			$arrReqData['mb_uid'] = $this->session->uid;

			$moneyhist_model = new MoneyHist_Model();
			$count = $moneyhist_model->searchCount($arrReqData);

			$result->data = $count;
			$result->status = STATUS_SUCCESS;
        }
		
		echo json_encode($result);

    }


	public function moneylog_page()
	{
		$jsonData = $_REQUEST['json_'];
		$arrReqData = json_decode($jsonData, true);

		$result = new \StdClass;
		if(!is_login())
		{
            $result->status = STATUS_LOGOUT;		
        } else {
			$arrReqData['mb_uid'] = $this->session->uid;

			$moneyhist_model = new MoneyHist_Model();
			$arrLog = $moneyhist_model->searchList($arrReqData, $arrReqData['page'], $arrReqData['cntper']);

			$result->data = $arrLog;
			$result->status = STATUS_SUCCESS;
        }
		
		echo json_encode($result);

    }



	
	//------------------------------------

	public function moneylist_count()
	{
		$jsonData = $_REQUEST['json_'];
		$arrReqData = json_decode($jsonData, true);

		$result = new \StdClass;
		if(!is_login())
		{
            $result->status = STATUS_LOGOUT;		
        } else {
			$moneyhist_model = new MoneyHist_Model();

			$uid = $this->session->uid;
            $objAdmin = $this->member_model->getByUid($uid);
			if(!$this->member_model->permittedMember($objAdmin) || $objAdmin->mb_level <= LEVEL_AGENCY){
				$result->status = STATUS_FAIL;	
			} else if(strlen($arrReqData['mb_uid']) < 1){
				$result->data = 0;
				$result->status = STATUS_SUCCESS;	
			}
			else{	
				$count = $moneyhist_model->searchCount($arrReqData);

				$result->data = $count;
				$result->status = STATUS_SUCCESS;
			}

			
        }
		
		echo json_encode($result);

    }


	public function moneylist_page()
	{
		$jsonData = $_REQUEST['json_'];
		$arrReqData = json_decode($jsonData, true);

		$result = new \StdClass;
		if(!is_login())
		{
            $result->status = STATUS_LOGOUT;		
        } else {
			$moneyhist_model = new MoneyHist_Model();

            $uid = $this->session->uid;
            $objAdmin = $this->member_model->getByUid($uid);
			if(!$this->member_model->permittedMember($objAdmin) || $objAdmin->mb_level <= LEVEL_AGENCY){
				$result->status = STATUS_FAIL;	
			} else if(strlen($arrReqData['mb_uid']) < 1){
				$result->data = NULL;
				$result->status = STATUS_SUCCESS;	
			}
			else {
				
				$arrLog = $moneyhist_model->searchList($arrReqData, $arrReqData['page'], $arrReqData['cntper']);
	
				$result->data = $arrLog;
				$result->status = STATUS_SUCCESS;	
			}
			
        }
		
		echo json_encode($result);

    }



	//------------------------------------

	
	public function maintain_change()
	{
		$jsonData = $_REQUEST['json_'];
		$arrReqData = json_decode($jsonData, true);

		$result = new \StdClass;
		if(!is_login())
		{
            $result->status = STATUS_LOGOUT;		
        } else {
			
            $uid = $this->session->uid;
            $objAdmin = $this->member_model->getByUid($uid);
			if(!$this->member_model->permittedMember($objAdmin) || $objAdmin->mb_level <= LEVEL_AGENCY){
				$result->status = STATUS_FAIL;	
			} 
			else {
				$arrReqData['conf_active'] = $arrReqData['bet_lock'];
				$bResult = $this->confsite_model->updateById(CONF_MAINTAIN, $arrReqData);
				
				$result->status = STATUS_SUCCESS;	
			}
			
        }
		
		echo json_encode($result);

    }

	
	public function bettime_change()
	{
		$jsonData = $_REQUEST['json_'];
		$arrReqData = json_decode($jsonData, true);

		$result = new \StdClass;
		if(!is_login())
		{
            $result->status = STATUS_LOGOUT;		
        } else {
			
            $uid = $this->session->uid;
            $objAdmin = $this->member_model->getByUid($uid);
			if(!$this->member_model->permittedMember($objAdmin) || $objAdmin->mb_level <= LEVEL_AGENCY){
				$result->status = STATUS_FAIL;	
			} 
			else {
				$confgame_model = new ConfGame_Model();

				$arrReqData['game_time_countdown'] = $arrReqData['bet_time'];
				$bResult = $confgame_model->updateById(intval($arrReqData['game']), $arrReqData);
				
				$result->status = STATUS_SUCCESS;	
			}
			
        }
		
		echo json_encode($result);

    }

	
	public function clean_db()
	{
		$jsonData = $_REQUEST['json_'];
		$arrReqData = json_decode($jsonData, true);

		$result = new \StdClass;
		if(!is_login())
		{
            $result->status = STATUS_LOGOUT;		
        } else {
			
            $uid = $this->session->uid;
            $objAdmin = $this->member_model->getByUid($uid);
			if(!$this->member_model->permittedMember($objAdmin) || $objAdmin->mb_level <= LEVEL_AGENCY){
				$result->status = STATUS_FAIL;	
			} 
			else {
				$clean_model = new Clean_Model();

				$bResult = $clean_model->cleanDb($arrReqData);
				
				if($bResult)
					$result->status = STATUS_SUCCESS;	
				else 
					$result->status = STATUS_FAIL;	
				
			}
			
        }
		
		echo json_encode($result);

    }

	///---------------------------------------------

	
	public function transfer_wait()
	{
		$result = new \StdClass;
		if(!is_login())
		{
            $result->status = STATUS_LOGOUT;		
        } else {
			$uid = $this->session->uid;
			$objMember = $this->member_model->getByUid($uid);
			$charge_model = new Charge_Model();
			$exchange_model = new Exchange_Model();
			

			if(!$this->member_model->permittedMember($objMember)){
				$result->status = STATUS_FAIL;
			} else {
				$emp_fid = $objMember->mb_level == LEVEL_AGENCY ? $objMember->mb_fid : 0;
				$emp_uid = $objMember->mb_level == LEVEL_AGENCY ? $objMember->mb_uid : SITE_MASTER_NAME;
			
				$arrData['level'] =  $objMember->mb_level;
				$arrData['charge'] =  $charge_model->waitProc($emp_fid);
				$arrData['exchange'] =  $exchange_model->waitProc($emp_fid);
				if($objMember->mb_level == LEVEL_AGENCY)
					$arrData['notice'] =  $this->notice_model->waitProc($emp_uid);
				else $arrData['notice'] = 0;

				$result->data = $arrData;
				$result->status = STATUS_SUCCESS;
			}
			
        }
		
		echo json_encode($result);

	}

}