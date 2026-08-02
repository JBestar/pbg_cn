<?php namespace App\Controllers;

use App\Models\Config_Model;
use App\Models\Captcha_Model;

class Pages extends BaseController
{
	public function index()
	{
		$this->response->redirect('/');
	}

    public function login()
	{		
		$captcha_model = new Captcha_Model();

		$captchaSource = DOWNLOADROOT."captcha_src".DIRECTORY_SEPARATOR;
		$captchaPath = DOWNLOADROOT."captcha".DIRECTORY_SEPARATOR;

		$arrCaptcha = [];
		getCaptchaFiles($captchaSource, $arrCaptcha);
				
		$nCount = count($arrCaptcha);
		$seed = microtime(true);

		$captcha = "";
		if($nCount > 0){
			$tmNow = time();
			
			mt_srand($seed); 
			$index = mt_rand(0, $nCount-1);	
			$captchaSrc = $arrCaptcha[$index];

			$captcha =  $seed;

			if(file_exists($captchaPath.$captcha.".jpg")) {
				unlink($captchaPath.$captcha.".jpg");
			}

			if( copy($captchaSource.$captchaSrc.".jpg", $captchaPath.$captcha.".jpg") ){
				$captcha_model->addLog($captcha, $captchaSrc);
			}
		}
		
		
        $siteName = $this->confsite_model->getSiteName();  

		echo view('pages/login', array("site_name"=>$siteName, "captcha"=>$captcha));		
		
	}

	public function logout(){
		// writeLog("Delete SessId=".$this->session->session_id);
		$this->sess_model->deleteById($this->session->session_id);
		$this->session->destroy();
		$this->response->redirect('/pages/login');
	}



	//--------------------------------------------------------------------

}