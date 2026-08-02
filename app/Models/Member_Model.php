<?php 
namespace App\Models;

use CodeIgniter\Model;
use Config\Database;

class Member_Model extends Model {

    private $mDb;
    private $mBuilder;
    private $mTbName = "member";
    private $mTbColumn;

    function __construct()
    {      
        $this->mDb      = Database::connect();
        $this->mBuilder = $this->mDb->table($this->mTbName);

        $this->mTbColumn = ['mb_fid', 'mb_uid', 'mb_level', 'mb_emp_fid', 'mb_nickname', 'mb_money', 'mb_point',
            'mb_lang', 'mb_time_join', 'mb_time_last', 'mb_ip_last', 'mb_game_pb_ratio', 'mb_state_active', 'mb_state_delete',
            'mb_limit_round', 'mb_limit_single', 'mb_limit_mix', 'mb_limit_three', 'mb_limit_digit' ];
    }

    public static function hashPassword($plain)
    {
        return password_hash((string)$plain, PASSWORD_DEFAULT);
    }

    public static function verifyPassword($plain, $hash)
    {
        $hash = (string)$hash;
        $plain = (string)$plain;
        if ($hash === '') {
            return false;
        }
        // Support legacy plain-text rows during transition
        if (strpos($hash, '$2y$') === 0 || strpos($hash, '$2a$') === 0 || strpos($hash, '$argon') === 0) {
            return password_verify($plain, $hash);
        }
        return hash_equals($hash, $plain);
    }

    public function getByFid($fid){
        
        try {
            
            $where = "mb_state_delete = '0' AND mb_fid = '".$fid."' ";

            $this->mBuilder ->select($this->mTbColumn)
                            ->where($where)
                            ->getCompiledSelect(false);
            $query = $this->mBuilder->get();
            return $query->getRow();
            
        } catch (\Exception $e) {  
            return NULL;
        }
        return NULL;
    }
    
    public function getByUid($uid){
        
        try {   
            
            $where = "mb_state_delete = '0' AND mb_uid = '".$uid."' ";

            $this->mBuilder ->select($this->mTbColumn)
                            ->where($where)
                            ->getCompiledSelect(false);
            $query = $this->mBuilder->get();
            return $query->getRow();
            
        } catch (\Exception $e) {  
            return NULL;
        }
        return NULL;
    }

    public function getAllByUid($uid){
        
        try {   
            
            $where = "mb_uid = '".$uid."' ";

            $this->mBuilder ->select($this->mTbColumn)
                            ->where($where)
                            ->getCompiledSelect(false);
            $query = $this->mBuilder->get();
            return $query->getRow();
            
        } catch (\Exception $e) {  
            return NULL;
        }
        return NULL;
    }

    public function getByName($mb_name, $mb_fid = 0){
        
        try { 
            $where = "mb_state_delete = '0' AND mb_nickname = '".$mb_name."' ";
            if($mb_fid > 0)
                $where.= "AND mb_fid != '".$mb_fid."' ";

            $this->mBuilder ->select($this->mTbColumn)
                            ->where($where)
                            ->getCompiledSelect(false);
            $query = $this->mBuilder->get();
            return $query->getRow();
            
        } catch (\Exception $e) {  
            return NULL;
        }
        return NULL;
    }
    
    public function getInfoByFid($fid){
        
        try {
            $where = "mb_state_delete = '0' AND mb_fid = '".$fid."' ";

            $this->mBuilder ->where($where)
                            ->getCompiledSelect(false);
            $query = $this->mBuilder->get();
            return $query->getRow();
            
        } catch (\Exception $e) {  
            return NULL;
        }
        return NULL;
    }

    
    public function getInfoByUid($uid){
        
        try {
            $where = "mb_state_delete = '0' AND mb_uid = '".$uid."' ";

            $this->mBuilder ->where($where)
                            ->getCompiledSelect(false);
            $query = $this->mBuilder->get();
            return $query->getRow();
            
        } catch (\Exception $e) {  
            return NULL;
        }
        return NULL;
    }

    public function getTotalAssets(){

        $strSql = " SELECT SUM(mb_money) AS money_sum,  SUM(mb_point) AS point_sum ";
        $strSql.= " FROM ".$this->mTbName;
        $strSql.= " WHERE mb_level <= '8' AND mb_state_delete = '0' ";
        return $this->mDb->query($strSql)->getRow();
    }
    
    public function deleteByUid($mb_uid){

        $this->mBuilder->set('mb_state_delete', 1);
        
        $this->mBuilder->where('mb_uid', $mb_uid);

        return $this->mBuilder->update();   //if success, return true
    }

    public function deleteByEmpFid($emp_fid){

        $this->mBuilder->set('mb_state_delete', 1);
        
        $this->mBuilder->where('mb_emp_fid', $emp_fid);

        return $this->mBuilder->update();   //if success, return true
    }

    
    public function removeByFid($mb_fid){

        
        $this->mBuilder->where('mb_fid', $mb_fid);

        return $this->mBuilder->delete();   //if success, return true
    }

    public function updateLast($objMember){

        $this->mBuilder->set('mb_time_last', 'NOW()', false);
        $this->mBuilder->set('mb_ip_last', $objMember->mb_ip_last);
        
        $this->mBuilder->where('mb_uid', $objMember->mb_uid);

        return $this->mBuilder->update();   //if success, return true
    }
   
    
    public function updatePermit($mb_uid, $permit){

        $this->mBuilder->set('mb_state_active', $permit);
        
        $this->mBuilder->where('mb_uid', $mb_uid);

        return $this->mBuilder->update();   //if success, return true
    }
    
    public function updateRest($mb_uid, $rest){

        $this->mBuilder->set('mb_rest', $rest);
        
        $this->mBuilder->where('mb_uid', $mb_uid);

        return $this->mBuilder->update();   //if success, return true
    }

    public function updatePwd($mb_uid, $pwd){

        $this->mBuilder->set('mb_pwd', self::hashPassword($pwd));
        
        $this->mBuilder->where('mb_uid', $mb_uid);

        return $this->mBuilder->update();   //if success, return true
    }

    public function updateLang($mb_uid, $lang)
    {
        $lang = strtolower(trim((string)$lang));
        if (!in_array($lang, ['ko', 'zh', 'en'], true)) {
            $lang = 'ko';
        }
        $this->mBuilder->set('mb_lang', $lang);
        $this->mBuilder->where('mb_uid', $mb_uid);
        return $this->mBuilder->update();
    }
    

    public function updateBank($mb_uid, $arrRqData){

        $this->mBuilder->set('mb_bank_name', $arrRqData['bank_name']);
        $this->mBuilder->set('mb_bank_num', $arrRqData['bank_num']);
        $this->mBuilder->set('mb_bank_owner', $arrRqData['bank_owner']);
        
        $this->mBuilder->where('mb_uid', $mb_uid);

        return $this->mBuilder->update();   //if success, return true
    }

    public function updateAssets($mb_fid, $nAddMoney, $nAddPoint = 0){

        if($nAddMoney == 0 && $nAddPoint == 0)
            return true;

        $strSql = "UPDATE ".$this->mTbName." SET ";
        if($nAddMoney != 0){
            $strSql.= "mb_money = mb_money";
            $strSql.= $nAddMoney > 0 ? " + ":" ";
            $strSql.= $nAddMoney;            
        }
        
        if($nAddPoint != 0){
            $strSql.= $nAddMoney != 0 ? " , ":" ";

            $strSql.= "mb_point = mb_point";
            $strSql.= $nAddPoint > 0 ? " + ":" ";
            $strSql.= $nAddPoint;
        }
        $strSql.= " WHERE mb_fid=".$mb_fid;

        return $this->mDb->simpleQuery($strSql);
    }

    public function login($uid, $pwd){
        
        try { 
            $where = "mb_uid = '".$this->mDb->escapeString($uid)."' ";

            $this->mBuilder ->select(array_merge($this->mTbColumn, ['mb_pwd', 'mb_bank_pwd']))
                            ->where($where)
                            ->getCompiledSelect(false);
            
            $query = $this->mBuilder->get();
            $row = $query->getRow();
            if (is_null($row) || !self::verifyPassword($pwd, $row->mb_pwd)) {
                return NULL;
            }
            // Rehash legacy plain passwords on successful login
            if (strpos((string)$row->mb_pwd, '$2y$') !== 0 && strpos((string)$row->mb_pwd, '$argon') !== 0) {
                $this->updatePwd($uid, $pwd);
            }
            unset($row->mb_pwd, $row->mb_bank_pwd);
            return $row;
            
        } catch (\Exception $e) {  
            return NULL;
        }
        return NULL;
    }

    
    public function login_bank($uid, $pwd){
        
        try { 
            $where = "mb_uid = '".$this->mDb->escapeString($uid)."' ";

            $this->mBuilder ->select(array_merge($this->mTbColumn, ['mb_bank_pwd']))
                            ->where($where)
                            ->getCompiledSelect(false);
            
            $query = $this->mBuilder->get();
            $row = $query->getRow();
            if (is_null($row) || !self::verifyPassword($pwd, $row->mb_bank_pwd)) {
                return NULL;
            }
            unset($row->mb_bank_pwd);
            return $row;
            
        } catch (\Exception $e) {  
            return NULL;
        }
        return NULL;
    }

    public function register($arrRqData, $objAdmin)
    {
        
        if(strlen($arrRqData['uid']) < 1 || strlen($arrRqData['nickname']) < 1 || 
            strlen($arrRqData['pwd']) < 1 || strlen($arrRqData['bank_pwd']) < 1 )
            return RESULT_ERROR;

        $objMember = $this->getAllByUid($arrRqData['uid']);
        if(!is_null($objMember)){
            if($objMember->mb_state_delete == 1){
                if( !$this->removeByFid($objMember->mb_fid)){
                    return RESULT_EXIST_ID; 
                }
            } else return RESULT_EXIST_ID;
        }
            

        $objMember = $this->getByName($arrRqData['nickname']);
        if(!is_null($objMember))
            return RESULT_EXIST_NAME;

        
        if($objAdmin->mb_level == LEVEL_AGENCY){
            $arrRqData['level'] = LEVEL_EMPLOYEE;
            
            $arrRqData['mb_emp_fid'] = $objAdmin->mb_fid;

            if(floatval($arrRqData['game_ratio']) > floatval($objAdmin->mb_game_pb_ratio) )
                return RESULT_OVERRATIO;
        } else if($objAdmin->mb_level > LEVEL_AGENCY){
            $arrRqData['level'] = LEVEL_AGENCY;
            $arrRqData['mb_emp_fid'] = 0;
        } else return RESULT_ERROR;


        $this->mBuilder->set('mb_uid', $arrRqData['uid']);
        $this->mBuilder->set('mb_pwd', self::hashPassword($arrRqData['pwd']));
        $this->mBuilder->set('mb_level', $arrRqData['level']);
        $this->mBuilder->set('mb_emp_fid', $arrRqData['mb_emp_fid']);
        $this->mBuilder->set('mb_nickname', $arrRqData['nickname']);
        $this->mBuilder->set('mb_time_join', 'NOW()', false);
        $this->mBuilder->set('mb_game_pb_ratio', floatval($arrRqData['game_ratio']));
        $this->mBuilder->set('mb_state_active', 1);
        $this->mBuilder->set('mb_state_delete', 0);
        $this->mBuilder->set('mb_lang', 'ko');
        $this->mBuilder->set('mb_phone', $arrRqData['phone']);
        $this->mBuilder->set('mb_bank_pwd', self::hashPassword($arrRqData['bank_pwd']));
        $this->mBuilder->set('mb_bank_name', $arrRqData['bank_name']);
        $this->mBuilder->set('mb_bank_num', $arrRqData['bank_num']);
        $this->mBuilder->set('mb_bank_owner', $arrRqData['bank_owner']);

        
        if($this->mBuilder->insert())
            return RESULT_OK;
        else RESULT_ERROR;
    }

    public function modify($arrRqData, $objAdmin)
    {
        
        if(strlen($arrRqData['uid']) < 1 || strlen($arrRqData['nickname']) < 1 || 
            strlen($arrRqData['pwd']) < 1 || strlen($arrRqData['bank_pwd']) < 1 )
            return RESULT_ERROR;

        
        if($objAdmin->mb_level == LEVEL_AGENCY){
            $arrRqData['level'] = LEVEL_EMPLOYEE;
            
            if(floatval($arrRqData['game_ratio']) > floatval($objAdmin->mb_game_pb_ratio) )
                return RESULT_OVERRATIO;
        } else if($objAdmin->mb_level > LEVEL_AGENCY){
            $arrRqData['level'] = LEVEL_AGENCY;
        } else return RESULT_ERROR;


        $this->mBuilder->set('mb_pwd', self::hashPassword($arrRqData['pwd']));
        $this->mBuilder->set('mb_game_pb_ratio', floatval($arrRqData['game_ratio']));
        if(array_key_exists('limit_round', $arrRqData) ){
            $this->mBuilder->set('mb_limit_round', $arrRqData['limit_round']);
            $this->mBuilder->set('mb_limit_single', $arrRqData['limit_single']);
            $this->mBuilder->set('mb_limit_mix', $arrRqData['limit_mix']);
            $this->mBuilder->set('mb_limit_three', $arrRqData['limit_three']);
            $this->mBuilder->set('mb_limit_digit', $arrRqData['limit_digit']);
        }
        $this->mBuilder->set('mb_phone', $arrRqData['phone']);
        $this->mBuilder->set('mb_bank_pwd', self::hashPassword($arrRqData['bank_pwd']));
        $this->mBuilder->set('mb_bank_name', $arrRqData['bank_name']);
        $this->mBuilder->set('mb_bank_num', $arrRqData['bank_num']);
        $this->mBuilder->set('mb_bank_owner', $arrRqData['bank_owner']);

        $this->mBuilder->where('mb_uid', $arrRqData['uid']);

        if($this->mBuilder->update())
            return RESULT_OK;
        else RESULT_ERROR;
    }


    public function permittedMember($objMember)
    {

        if(is_null($objMember))
            return false;

        if($objMember->mb_state_delete == 1 || $objMember->mb_state_active == 0 
                    || $objMember->mb_level < LEVEL_AGENCY)
            return false;

        return true;

    }


    public function isSubMember($objAdmin, $member_uid)
    {
        
        $objMember = $this->getByUid($member_uid);
        
        if(is_null($objMember) || is_null($objAdmin))
            return false;

        if($objMember->mb_level == LEVEL_EMPLOYEE && $objMember->mb_emp_fid !== $objAdmin->mb_fid)
            return false;
        
        return true;

    }

    public function getSubs($objAdmin)
    {
        $where =  "mb_state_delete = '0' ";
        if($objAdmin->mb_level == LEVEL_AGENCY){
            $where .= " AND mb_level = '".LEVEL_EMPLOYEE."' ";
            $where .= " AND mb_emp_fid = '".$objAdmin->mb_fid."' ";
        } else if($objAdmin->mb_level > LEVEL_AGENCY){
            $where .= " AND mb_level = '".LEVEL_AGENCY."' ";
        } else return NULL;
        
        try { 
            $tbColumn = ['mb_fid', 'mb_uid', 'mb_level', 'mb_emp_fid', 'mb_nickname', 'mb_money', 'mb_point' ];

            $this->mBuilder ->select($tbColumn)
                            ->where($where)
                            ->getCompiledSelect(false);
            
            $query = $this->mBuilder->get();
            
            return $query->getResult();
            
        } catch (\Exception $e) {  
            return NULL;
        }
        return NULL;
        

    }

    public function searchList($arrRqData, $level ){


        $where_member = " WHERE mb_level = '".$level."' AND mb_state_delete = '0' ";
        $where_bet = " WHERE bet_state > '0'  ";
        $where_charge = " WHERE charge_action_state = '1' ";
        $where_exchange = " WHERE exchange_action_state = '1' ";
        if(array_key_exists('mb_uid', $arrRqData) && strlen($arrRqData['mb_uid']) > 0){
            $where_member .= " AND mb_uid = '".$arrRqData['mb_uid']."' ";    
        }
        if($level == LEVEL_EMPLOYEE){
            $where_member .= " AND mb_emp_fid = '".$arrRqData['mb_emp_fid']."' ";    
        } 

        if( array_key_exists('start', $arrRqData) && strlen($arrRqData['start']) > 0 ){
            $where_bet .= " AND bet_time >= '".$arrRqData['start']."' ";
            $where_charge .= " AND charge_time_require >= '".$arrRqData['start']."' ";
            $where_exchange .= " AND exchange_time_require >= '".$arrRqData['start']."' ";

        }
        if(array_key_exists('end', $arrRqData) && strlen($arrRqData['end']) > 0){
            $where_bet .= " AND bet_time <= '".$arrRqData['end']." 23:59:59' ";    
            $where_charge .= " AND charge_time_require <= '".$arrRqData['end']." 23:59:59' ";
            $where_exchange .= " AND exchange_time_require <= '".$arrRqData['end']." 23:59:59' ";
        }            
            
        $strSql = "SELECT mb_fid, mb_uid, mb_level, mb_emp_fid, mb_nickname, mb_money, mb_point, mb_time_join, mb_time_last, ";
        $strSql.= " mb_ip_last, mb_game_pb_ratio, mb_state_active, mb_limit_round, mb_limit_single, mb_limit_mix, mb_limit_three, mb_limit_digit, mb_color, mb_rest, ";

        if($level == LEVEL_EMPLOYEE){
            $strSql.= " mb_emp_uid, mb_emp_nickname, "; 
        } else {
            $strSql.= " mb_user_count, mb_user_money, ";
        }

	    $strSql.= " bet_sum, bet_win_sum, bet_empl_sum, bet_agen_sum, "; 
        $strSql.= " charge_default_sum, charge_present_sum, exchange_default_sum, exchange_present_sum ";
        $strSql.= " FROM " .$this->mTbName;

        if($level == LEVEL_EMPLOYEE){
            $strSql.= " LEFT JOIN ( SELECT mb_fid AS emp_fid , mb_uid AS mb_emp_uid, mb_nickname AS mb_emp_nickname FROM " .$this->mTbName;
                $strSql.= " ) AS emp_tb ON emp_tb.emp_fid = member.mb_emp_fid ";

            $strSql.= " LEFT JOIN ( SELECT bet_mb_uid, SUM(bet_money) AS bet_sum, SUM(bet_win_money) AS bet_win_sum, SUM(bet_empl_amount) AS bet_empl_sum, ";
                $strSql.= " SUM(bet_agen_amount) AS bet_agen_sum FROM bet_powerball ";
                $strSql.= $where_bet;
                $strSql.= " GROUP BY bet_mb_uid ) AS bet_tb ON bet_tb.bet_mb_uid = member.mb_uid ";

        } else {
            $strSql.= " LEFT JOIN ( SELECT mb_emp_fid AS emp_fid , COUNT(mb_fid) as mb_user_count, SUM(mb_money) as mb_user_money  FROM " .$this->mTbName;
                $strSql.= " WHERE mb_level = '7' AND mb_state_delete = '0' GROUP BY mb_emp_fid ) AS emp_tb ON emp_tb.emp_fid = member.mb_fid ";


            $strSql.= " LEFT JOIN ( SELECT bet_emp_fid, SUM(bet_money) AS bet_sum, SUM(bet_win_money) AS bet_win_sum, SUM(bet_empl_amount) AS bet_empl_sum, ";
                $strSql.= " SUM(bet_agen_amount) AS bet_agen_sum FROM bet_powerball ";
                $strSql.= $where_bet;
                $strSql.= " GROUP BY bet_emp_fid ) AS bet_tb ON bet_tb.bet_emp_fid = member.mb_fid ";
        }
        
        
        $strSql.= " LEFT JOIN ( SELECT charge_mb_uid, SUM(charge_money) AS charge_default_sum FROM member_charge ";
            $strSql.= $where_charge." AND charge_type = '0' ";
            $strSql.= " GROUP BY charge_mb_uid ) AS charge_default_tb ON charge_default_tb.charge_mb_uid = member.mb_uid ";
        
        $strSql.= " LEFT JOIN ( SELECT charge_mb_uid, SUM(charge_money) AS charge_present_sum FROM member_charge ";
            $strSql.= $where_charge."  AND charge_type = '1' ";
	        $strSql.= " GROUP BY charge_mb_uid ) AS charge_present_tb ON charge_present_tb.charge_mb_uid = member.mb_uid ";
        
        $strSql.= " LEFT JOIN ( SELECT exchange_mb_uid, SUM(exchange_money) AS exchange_default_sum FROM member_exchange ";
            $strSql.= $where_exchange." AND exchange_type = 0 ";
            $strSql.= " GROUP BY exchange_mb_uid ) AS exchange_default_tb ON exchange_default_tb.exchange_mb_uid = member.mb_uid ";
        
        $strSql.= " LEFT JOIN ( SELECT exchange_mb_uid, SUM(exchange_money) AS exchange_present_sum FROM member_exchange ";
            $strSql.= $where_exchange." AND exchange_type = 1 ";
	        $strSql.= " GROUP BY exchange_mb_uid ) AS exchange_present_tb ON exchange_present_tb.exchange_mb_uid = member.mb_uid ";
        

        $strSql.= $where_member; 

        return $this->mDb->query($strSql)->getResult();
    }



}