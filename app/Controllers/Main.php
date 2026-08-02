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
			echo view('main/main_header', array("site_name"=>$siteName));
			echo view('main/main_menu', $arrItem);		
			echo view('main/term_list', array("arrMember"=>$arrMember, "adminLevel"=>$objMember->mb_level));		
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


			$siteName = $this->confsite_model->getSiteName();
			$arrItem = getSidebarArray();
			$arrItem['menuitem_2'] = " spanActiveMenu";
			$arrItem['mb_level'] = $objMember->mb_level;

			echo view('main/main_header', array("site_name"=>$siteName));
			echo view('main/main_menu', $arrItem);		
			echo view('main/game_result');		
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

			echo view('main/main_header', array("site_name"=>$siteName));
			echo view('main/main_menu', $arrItem);		
			echo view('main/bet_list');		
			echo view('main/main_footer');	
		}
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

	public function charge_list()
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
			$arrItem['menuitem_6'] = " spanActiveMenu";
			$arrItem['mb_level'] = $objMember->mb_level;

			echo view('main/main_header', array("site_name"=>$siteName));
			echo view('main/main_menu', $arrItem);		
			echo view('main/charge_list');
			echo view('main/main_footer');	
		}
	}

	public function exchange_list()
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
			$arrItem['menuitem_7'] = " spanActiveMenu";
			$arrItem['mb_level'] = $objMember->mb_level;

			echo view('main/main_header', array("site_name"=>$siteName));
			echo view('main/main_menu', $arrItem);		
			echo view('main/exchange_list');
			echo view('main/main_footer');
		}	
	}

	
	public function money_log()
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
			$arrItem['menuitem_8'] = " spanActiveMenu";
			$arrItem['mb_level'] = $objMember->mb_level;
			
			echo view('main/main_header', array("site_name"=>$siteName));
			echo view('main/main_menu', $arrItem);		
			echo view('main/money_log');
			echo view('main/main_footer');	
		}
	}

	
	public function moneylog_list()
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
			$arrItem['menuitem_8'] = " spanActiveMenu";
			$arrItem['mb_level'] = $objMember->mb_level;
			
			echo view('main/main_header', array("site_name"=>$siteName));
			echo view('main/main_menu', $arrItem);		
			echo view('main/moneylog_list');
			echo view('main/main_footer');	
		}
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