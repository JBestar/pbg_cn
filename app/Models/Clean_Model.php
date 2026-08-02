<?php 
namespace App\Models;

use CodeIgniter\Model;
use Config\Database;

class Clean_Model extends Model {

    private $mDb;
    private $mBuilder;
    private $mTbName = "bet_powerball";

    function __construct()
    {
      
        $this->mDb      = Database::connect();
        $this->mBuilder = $this->mDb->table($this->mTbName);

    }


    //디비정리
    function cleanDb($arrReqData){
        
        $strDate = date('Y-m-d');
        
        if(!array_key_exists("date", $arrReqData))
            return false;
        if(strlen($arrReqData['date']) < 1 )
            return false;

        $strDate = $arrReqData['date'];

        $strSql = " DELETE FROM bet_powerball WHERE bet_time < '".$strDate."' ";
        if(!$this->mDb->simpleQuery($strSql))
            return false;
        
        $strSql = " DELETE FROM round_powerball WHERE round_date < '".$strDate."' ";
        $this -> mDb -> simpleQuery($strSql);

        $strSql = " DELETE FROM round_coin5 WHERE round_date < '".$strDate."' ";
        $this -> mDb -> simpleQuery($strSql);

        $strSql = " DELETE FROM board_notice WHERE notice_create_time < '".$strDate."' ";
        $this -> mDb -> simpleQuery($strSql);
        
        $strSql = " DELETE FROM log_history WHERE log_time < '".$strDate."' ";
        $this -> mDb -> simpleQuery($strSql);
        
        $strSql = " DELETE FROM member_charge WHERE charge_time_require < '".$strDate."' ";
        $this -> mDb -> simpleQuery($strSql);
        
        $strSql = " DELETE FROM member_exchange WHERE exchange_time_require < '".$strDate."' ";
        $this -> mDb -> simpleQuery($strSql);
        
        $strSql = " DELETE FROM money_history WHERE money_update_time < '".$strDate."' ";
        $this -> mDb -> simpleQuery($strSql);
        
        $strSql = " DELETE FROM log_captcha WHERE log_time < '".$strDate."' ";
        $this -> mDb -> simpleQuery($strSql);
        
        return true;
    }
    //디비초기화
    function initDb(){
  
        return true;
    }


}