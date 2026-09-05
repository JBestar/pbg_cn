<?php namespace App\Controllers;
use App\Models\ConfGame_Model;

class Main extends BaseController
{

	public function __construct()
    {
        
    }

	public function index()
	{
		if(!is_login())
		{
			$this->response->redirect('/pages/login');			
		}
		else {
			$this->response->redirect('/Main/term_list');
		}
	}


	public function term_list()
	{	
		if(!is_login())
		{
			$this->response->redirect('/pages/login');			
		}
		else {
			$uid = $this->session->uid;
            $objMember = $this->member_model->getByUid($uid);
		
			
			$arrMember = $this->member_model->getSubs($objMember);
				

			$arrItem = getSidebarArray();
			$arrItem['menuitem_1'] = " spanActiveMenu";
			$arrItem['mb_level'] = $objMember->mb_level;

			$siteName = $this->confsite_model->getSiteName();
			$viewData = [
				'arrMember' => $arrMember,
				'adminLevel' => $objMember->mb_level,
			];
			echo view('main/main_header', array("site_name"=>$siteName));
			echo view('main/main_menu', $arrItem);		
			echo view('main/term_list', $viewData);		
			echo view('main/main_footer');	
		}
	}

	public function sub_charge()
	{	
		if(!is_login())
		{
			$this->response->redirect('/pages/login');			
		}
		else {
			$uid = $this->session->uid;
            $objMember = $this->member_model->getByUid($uid);

        	$siteName = $this->confsite_model->getSiteName();
			$arrItem = getSidebarArray();
			$arrItem['mb_level'] = $objMember->mb_level;

			echo view('main/main_header', array("site_name"=>$siteName));
			echo view('main/main_menu', $arrItem);		
			echo view('main/sub_charge');		
			echo view('main/main_footer');	
		}
	}

	public function sub_exchange()
	{	
		if(!is_login())
		{
			$this->response->redirect('/pages/login');			
		}
		else {
			$uid = $this->session->uid;
            $objMember = $this->member_model->getByUid($uid);

        	$siteName = $this->confsite_model->getSiteName();
			$arrItem = getSidebarArray();
			$arrItem['mb_level'] = $objMember->mb_level;

			echo view('main/main_header', array("site_name"=>$siteName));
			echo view('main/main_menu', $arrItem);		
			echo view('main/sub_exchange');		
			echo view('main/main_footer');	
		}
	}

	public function setting()
	{	
		if(!is_login())
		{
			$this->response->redirect('/pages/login');			
		}
		else {
			$uid = $this->session->uid;
            $objMember = $this->member_model->getInfoByUid($uid);

        	$siteName = $this->confsite_model->getSiteName();
			$arrItem = getSidebarArray();
			$arrItem['mb_level'] = $objMember->mb_level;


			$arrSetting['mb_level'] = $objMember->mb_level;
			if($objMember->mb_level == LEVEL_AGENCY){
				$arrSetting['mb_bank_name'] = $objMember->mb_bank_name;
				$arrSetting['mb_bank_owner'] = $objMember->mb_bank_owner;
				$arrSetting['mb_bank_num'] = $objMember->mb_bank_num;
				
			} else{
				$confgame_model = new ConfGame_Model();
				
				$arrSetting['isMaintain'] = $this->confsite_model->IsMaintain();

				$objConfPb = $confgame_model->getById(GAME_POWER_BALL);
				$arrSetting['bet_time1'] = $objConfPb->game_time_countdown;

				$objConfPb = $confgame_model->getById(GAME_COIN5_BALL);
				$arrSetting['bet_time2'] = $objConfPb->game_time_countdown;

				$objConfPb = $confgame_model->getById(GAME_BOGLE_BALL);
				$arrSetting['bet_time3'] = $objConfPb->game_time_countdown;
			}

			echo view('main/main_header', array("site_name"=>$siteName));
			echo view('main/main_menu', $arrItem);		
			echo view('main/setting' , $arrSetting);		
			echo view('main/main_footer');
		}	
	}


	public function game_result()
	{	
		if(!is_login())
		{
			$this->response->redirect('/pages/login');			
		}
		else {
			$uid = $this->session->uid;
            $objMember = $this->member_model->getByUid($uid);

			$arrMember = $this->member_model->getSubs($objMember);
			if (!is_array($arrMember) && !is_object($arrMember)) {
				$arrMember = [];
			}

			$siteName = $this->confsite_model->getSiteName();
			$arrItem = getSidebarArray();
			$arrItem['menuitem_2'] = " spanActiveMenu";
			$arrItem['mb_level'] = $objMember->mb_level;

			$viewName = ($objMember->mb_level == LEVEL_AGENCY)
				? 'main/game_result_agency'
				: 'main/game_result_hq';

			echo view('main/main_header', array("site_name"=>$siteName));
			echo view('main/main_menu', $arrItem);		
			echo view($viewName, [
				'arrMember' => $arrMember,
				'adminLevel' => $objMember->mb_level,
			]);		
			echo view('main/main_footer');	
		}
	}

	public function bet_list()
	{	
		if(!is_login())
		{
			$this->response->redirect('/pages/login');			
		}
		else {
			$uid = $this->session->uid;
            $objMember = $this->member_model->getByUid($uid);

        	$siteName = $this->confsite_model->getSiteName();
			$arrItem = getSidebarArray();
			$arrItem['menuitem_3'] = " spanActiveMenu";
			$arrItem['mb_level'] = $objMember->mb_level;

			$viewName = ($objMember->mb_level == LEVEL_AGENCY)
				? 'main/bet_list'
				: 'main/bet_list_hq';

			echo view('main/main_header', array("site_name"=>$siteName));
			echo view('main/main_menu', $arrItem);		
			echo view($viewName, [
				'adminLevel' => $objMember->mb_level,
			]);		
			echo view('main/main_footer');	
		}
	}

	/** 배팅내역 상세보기 팝업 */
	public function bet_detail()
	{
		if (!is_login()) {
			$this->response->redirect('/pages/login');
			return;
		}
		$uid = $this->request->getGet('uid');
		$start = $this->request->getGet('start');
		$end = $this->request->getGet('end');
		if (!$start) {
			$start = date('Y-m-d');
		}
		if (!$end) {
			$end = date('Y-m-d');
		}
		$siteName = $this->confsite_model->getSiteName();
		echo view('main/bet_detail', [
			'site_name' => $siteName,
			'detail_uid' => (string)$uid,
			'start' => $start,
			'end' => $end,
		]);
	}

	public function bet_sum()
	{	
		if(!is_login())
		{
			$this->response->redirect('/pages/login');			
		}
		else {
			$uid = $this->session->uid;
            $objMember = $this->member_model->getByUid($uid);

        	$siteName = $this->confsite_model->getSiteName();
			$arrItem = getSidebarArray();
			$arrItem['menuitem_4'] = " spanActiveMenu";
			$arrItem['mb_level'] = $objMember->mb_level;

			echo view('main/main_header', array("site_name"=>$siteName));
			echo view('main/main_menu', $arrItem);		
			echo view('main/bet_sum');		
			echo view('main/main_footer');
		}	
	}
	
	public function bet_chg()
	{	
		if(!is_login())
		{
			$this->response->redirect('/pages/login');			
		}
		else {
			$uid = $this->session->uid;
            $objMember = $this->member_model->getByUid($uid);

			if(is_null($objMember) || $objMember->mb_level < LEVEL_ADMIN){
				$this->response->redirect('/');
			} else {
				$siteName = $this->confsite_model->getSiteName();
				$arrItem = getSidebarArray();
				$arrItem['mb_level'] = $objMember->mb_level;
	
				echo view('main/main_header', array("site_name"=>$siteName));
				echo view('main/main_menu', $arrItem);		
				echo view('main/bet_chg');		
				echo view('main/main_footer');	
			}
		}
	}

	public function member_list()
	{	
		if(!is_login())
		{
			$this->response->redirect('/pages/login');			
		}
		else {
			$uid = $this->session->uid;
            $objMember = $this->member_model->getByUid($uid);

        	$siteName = $this->confsite_model->getSiteName();
			$arrItem = getSidebarArray();
			$arrItem['menuitem_5'] = " spanActiveMenu";
			$arrItem['mb_level'] = $objMember->mb_level;

			echo view('main/main_header', array("site_name"=>$siteName));
			echo view('main/main_menu', $arrItem);		
			if($objMember->mb_level == LEVEL_AGENCY)
				echo view('main/agency');	//매장관리
			else if($objMember->mb_level > LEVEL_AGENCY)
				echo view('main/company');	//총판관리
			echo view('main/main_footer');	
		}
	}

	public function store_reg()
	{
		if (!is_login()) {
			$this->response->redirect('/pages/login');
			return;
		}
		echo view('main/store_reg');
	}

	public function store_edit()
	{
		if (!is_login()) {
			$this->response->redirect('/pages/login');
			return;
		}
		$fid = (int)$this->request->getGet('fid');
		echo view('main/store_edit', ['edit_fid' => $fid]);
	}

	public function store_charge()
	{
		if (!is_login()) {
			$this->response->redirect('/pages/login');
			return;
		}
		$uid = (string)$this->request->getGet('uid');
		echo view('main/store_charge', ['target_uid' => $uid]);
	}

	public function store_recover()
	{
		if (!is_login()) {
			$this->response->redirect('/pages/login');
			return;
		}
		$uid = (string)$this->request->getGet('uid');
		echo view('main/store_recover', ['target_uid' => $uid]);
	}

	/** 구매취소내역 */
	public function cancel_list()
	{
		if (!is_login()) {
			$this->response->redirect('/pages/login');
			return;
		}
		$uid = $this->session->uid;
		$objMember = $this->member_model->getByUid($uid);
		$siteName = $this->confsite_model->getSiteName();
		$arrItem = getSidebarArray();
		$arrItem['menuitem_8'] = " spanActiveMenu";
		$arrItem['mb_level'] = $objMember->mb_level;

		echo view('main/main_header', array("site_name" => $siteName));
		echo view('main/main_menu', $arrItem);
		echo view('main/cancel_list');
		echo view('main/main_footer');
	}

	/** 매장 충환전 내역 */
	public function store_ce_list()
	{
		if (!is_login()) {
			$this->response->redirect('/pages/login');
			return;
		}
		$uid = $this->session->uid;
		$objMember = $this->member_model->getByUid($uid);
		$siteName = $this->confsite_model->getSiteName();
		$arrItem = getSidebarArray();
		$arrItem['menuitem_6'] = " spanActiveMenu";
		$arrItem['mb_level'] = $objMember->mb_level;

		echo view('main/main_header', array("site_name" => $siteName));
		echo view('main/main_menu', $arrItem);
		echo view('main/store_ce_list');
		echo view('main/main_footer');
	}

	/** 총판 충환전 내역 */
	public function agency_ce_list()
	{
		if (!is_login()) {
			$this->response->redirect('/pages/login');
			return;
		}
		$uid = $this->session->uid;
		$objMember = $this->member_model->getByUid($uid);
		$siteName = $this->confsite_model->getSiteName();
		$arrItem = getSidebarArray();
		$arrItem['menuitem_7'] = " spanActiveMenu";
		$arrItem['mb_level'] = $objMember->mb_level;

		echo view('main/main_header', array("site_name" => $siteName));
		echo view('main/main_menu', $arrItem);
		echo view('main/agency_ce_list', [
			'show_grade' => ($objMember->mb_level > LEVEL_AGENCY),
		]);
		echo view('main/main_footer');
	}

	/** 매장 충환전 상세 팝업 */
	public function store_ce_detail()
	{
		if (!is_login()) {
			$this->response->redirect('/pages/login');
			return;
		}
		$uid = $this->request->getGet('uid');
		$start = $this->request->getGet('start') ?: date('Y-m-d', strtotime('-6 months'));
		$end = $this->request->getGet('end') ?: date('Y-m-d');
		echo view('main/ce_detail', [
			'detail_uid' => (string)$uid,
			'start' => $start,
			'end' => $end,
			'scope' => 'store',
			'title_key' => 'title_store_ce_detail',
		]);
	}

	/** 총판 충환전 상세 팝업 */
	public function agency_ce_detail()
	{
		if (!is_login()) {
			$this->response->redirect('/pages/login');
			return;
		}
		$uid = $this->request->getGet('uid');
		$start = $this->request->getGet('start') ?: date('Y-m-d', strtotime('-6 months'));
		$end = $this->request->getGet('end') ?: date('Y-m-d');
		echo view('main/ce_detail', [
			'detail_uid' => (string)$uid,
			'start' => $start,
			'end' => $end,
			'scope' => 'agency',
			'title_key' => 'title_agency_ce_detail',
		]);
	}

	/** @deprecated 사이드바에서 제거됨 — 매장 충환전으로 리다이렉트 */
	public function charge_list()
	{
		$this->response->redirect('/Main/store_ce_list');
	}

	/** @deprecated 사이드바에서 제거됨 — 총판 충환전으로 리다이렉트 */
	public function exchange_list()
	{
		$this->response->redirect('/Main/agency_ce_list');
	}

	/** @deprecated */
	public function money_log()
	{
		$this->response->redirect('/Main/store_ce_list');
	}

	/** @deprecated */
	public function moneylog_list()
	{
		$this->response->redirect('/Main/agency_ce_list');
	}

	public function memo_list()
	{	
		if(!is_login())
		{
			$this->response->redirect('/pages/login');			
		}
		else {
			$uid = $this->session->uid;
            $objMember = $this->member_model->getByUid($uid);

        	$siteName = $this->confsite_model->getSiteName();
			$arrItem = getSidebarArray();
			$arrItem['menuitem_9'] = " spanActiveMenu";
			$arrItem['mb_level'] = $objMember->mb_level;
			
			echo view('main/main_header', array("site_name"=>$siteName));
			echo view('main/main_menu', $arrItem);		
			echo view('main/memo_list');
			echo view('main/main_footer');	
		}
	}

	
	public function qna_list()
	{	
		if(!is_login())
		{
			$this->response->redirect('/pages/login');			
		}
		else {
			$uid = $this->session->uid;
            $objMember = $this->member_model->getByUid($uid);

        	$siteName = $this->confsite_model->getSiteName();
			$arrItem = getSidebarArray();
			$arrItem['menuitem_10'] = " spanActiveMenu";
			$arrItem['mb_level'] = $objMember->mb_level;
			
			echo view('main/main_header', array("site_name"=>$siteName));
			echo view('main/main_menu', $arrItem);		
			echo view('main/qna_list');
			echo view('main/main_footer');	
		}
	}



}