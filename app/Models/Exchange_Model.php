<?php 
namespace App\Models;

use CodeIgniter\Model;
use Config\Database;

class Exchange_Model extends Model {

    private $mDb;
    private $mBuilder;
    private $mTbName = "member_exchange";
    private $mTbColumn;

    function __construct()
    {
      
        $this->mDb      = Database::connect();
        $this->mBuilder = $this->mDb->table($this->mTbName);
        $this->mTbColumn = ['exchange_fid', 'exchange_emp_fid', 'exchange_mb_uid', 'exchange_type', 
                    'exchange_money', 'exchange_time_require', 'exchange_action_state', 'exchange_time_process', 
                    'exchange_bank_name', 'exchange_bank_owner', 'exchange_bank_number', 'exchange_money_before', 'exchange_money_after'];

    }

    public function getById($exchange_id){
        
        try { 
            
            $this->mBuilder->where('exchange_fid', $exchange_id)
                           ->getCompiledSelect(false);
            $query = $this->mBuilder->get();
            return $query->getRow();
            
        } catch (\Exception $e) {  
            return NULL;
        }
        return NULL;
    }

    
    public function waitExchange($mb_uid){
        
        try { 
            $where = " exchange_client_delete = '0' AND ";
            $where.= " exchange_type = '".CHARGE_TYPE_DEFAULT."' AND";
            $where.= " exchange_action_state = '".CHARGE_STATE_WAIT."' AND";
            $where.= " exchange_mb_uid = '".$mb_uid."' ";
            
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

    public function addExchange($objUser, $arrRqData){

    	if(is_null($objUser)) return false;
    	if($arrRqData['amount'] < 1) return false;
    	
    	 //자료기지 등록
    	$this->mBuilder->set('exchange_emp_fid', $objUser->mb_emp_fid);
        $this->mBuilder->set('exchange_mb_uid', $objUser->mb_uid);
        $this->mBuilder->set('exchange_type', CHARGE_TYPE_DEFAULT);
        $this->mBuilder->set('exchange_money', $arrRqData['amount']);
        $this->mBuilder->set('exchange_time_require', 'NOW()', false);
        //1:요청, 2:처리완료 3:거절
        $this->mBuilder->set('exchange_action_state', CHARGE_STATE_WAIT);
        $this->mBuilder->set('exchange_bank_name', $arrRqData['bank_name']);
        $this->mBuilder->set('exchange_bank_owner', $arrRqData['bank_owner']);
        $this->mBuilder->set('exchange_bank_number', $arrRqData['bank_number']);

        $this->mBuilder->set('exchange_money_before', $objUser->mb_money);
        if(array_key_exists('exchange_money_after', $arrRqData))
            $this->mBuilder->set('exchange_money_after', $arrRqData['exchange_money_after']);

        return $this->mBuilder->insert();
        
    }

    
    public function addRecovery($objUser, $objAdmin, $arrRqData){

    	if(is_null($objUser)) return false;
    	if($arrRqData['money'] < 1) return false;
    	
        $nMoney = intval($arrRqData['money']);

    	 //자료기지 등록
    	$this->mBuilder->set('exchange_emp_fid', $objUser->mb_emp_fid);
        $this->mBuilder->set('exchange_mb_uid', $objUser->mb_uid);
        $this->mBuilder->set('exchange_type', CHARGE_TYPE_PRESENT);
        $this->mBuilder->set('exchange_money', $nMoney);
        $this->mBuilder->set('exchange_time_require', 'NOW()', false);
        //1:요청, 2:처리완료 3:거절
        $this->mBuilder->set('exchange_action_state', CHARGE_STATE_PERMIT);
        $this->mBuilder->set('exchange_action_uid', $objAdmin->mb_uid); 
        $this->mBuilder->set('exchange_money_before', $objUser->mb_money);        
        $this->mBuilder->set('exchange_money_after', $objUser->mb_money - $nMoney);
        $this->mBuilder->set('exchange_time_process', 'NOW()', false);

        return $this->mBuilder->insert();
        
    }
    
    public function procExchange($objExchange, $objUser, $actionState){

    	if(is_null($objUser)) return false;
    	
        $this->mBuilder->set('exchange_action_uid', $objUser->mb_uid);        
        $this->mBuilder->set('exchange_action_state', $actionState);
        $this->mBuilder->set('exchange_time_process', 'NOW()', false);

        $this->mBuilder->where('exchange_fid', $objExchange->exchange_fid);

        return $this->mBuilder->update();   //if success, return true
        
    }
    

    public function waitProc($emp_fid){

    	try { 
            $where = " exchange_state_delete = '0' AND ";
            $where.= " exchange_type = '".CHARGE_TYPE_DEFAULT."' AND";
            $where.= " exchange_action_state = '".CHARGE_STATE_WAIT."' AND";
            $where.= " exchange_emp_fid = '".$emp_fid."' ";

            $this->mBuilder ->select($this->mTbColumn)
                            ->where($where)
                            ->getCompiledSelect(false);
            
            return $this->mBuilder->countAllResults();
            
        } catch (\Exception $e) {  
            return 0;
        }
        return 0;
        
    }

    /**
     * Pending Default-type exchanges for emp_fid, keyed by exchange_mb_uid (latest per uid).
     * @return array<string, object>
     */
    public function mapWaitDefaultByEmpFid($emp_fid)
    {
        $map = [];
        try {
            $where = " exchange_state_delete = '0' AND ";
            $where .= " exchange_type = '".CHARGE_TYPE_DEFAULT."' AND";
            $where .= " exchange_action_state = '".CHARGE_STATE_WAIT."' AND";
            $where .= " exchange_emp_fid = '".intval($emp_fid)."' ";
            $rows = $this->mBuilder->select($this->mTbColumn)
                ->where($where)
                ->orderBy('exchange_fid', 'DESC')
                ->get()
                ->getResult();
            foreach ($rows as $row) {
                $uid = (string)$row->exchange_mb_uid;
                if ($uid !== '' && !isset($map[$uid])) {
                    $map[$uid] = $row;
                }
            }
        } catch (\Exception $e) {
            return [];
        }
        return $map;
    }

    public function deleteExchange($exchange_id){

        $this->mBuilder->set('exchange_client_delete', '1');

        $this->mBuilder->where('exchange_fid', $exchange_id);

        return $this->mBuilder->update();   //if success, return true
    }


    public function deleteExchangeProc($exchange_id){

        $this->mBuilder->set('exchange_state_delete', '1');

        $this->mBuilder->where('exchange_fid', $exchange_id);

        return $this->mBuilder->update();   //if success, return true
    }

    function searchCount($arrRqData){
        
        try { 
            
            $where = "exchange_client_delete = '0' ";
            $where.= "AND exchange_type = '".CHARGE_TYPE_DEFAULT."' ";
            if(array_key_exists('mb_uid', $arrRqData)){
                $where .= " AND exchange_mb_uid = '".$arrRqData['mb_uid']."' ";    
            }
            $this->mBuilder ->where($where)            
                            ->getCompiledSelect(false);

            return $this->mBuilder->countAllResults();
            
        } catch (\Exception $e) {  
            return 0;
        }
        return 0;
    }

    function searchList($arrRqData, $page, $cntPer = 20){
        
        try { 
            
            if($page < 1)
                return NULL;
            if($cntPer < 1)
                return NULL;

            $where = "exchange_client_delete = '0' ";
            $where.= "AND exchange_type = '".CHARGE_TYPE_DEFAULT."' ";
            if(array_key_exists('mb_uid', $arrRqData)){
                $where .= " AND exchange_mb_uid = '".$arrRqData['mb_uid']."' ";    
            }             

            $this->mBuilder ->select($this->mTbColumn)     
                            ->where($where)
                            ->orderBy('exchange_fid', 'DESC')
                            ->getCompiledSelect(false);

            $query = $this->mBuilder->get($cntPer, $cntPer*($page-1));
            return $query->getResult();
            
        } catch (\Exception $e) {  
            return NULL;
        }
        return NULL;
    }

    function searchProcCount($arrRqData){
        
        try { 
            
            $where = "exchange_state_delete = '0' ";
            if(strlen($arrRqData['start']) > 0){
                $where .= " AND exchange_time_require >= '".$arrRqData['start']."' ";    
            }
            if(strlen($arrRqData['end']) > 0){
                $where .= " AND exchange_time_require <= '".$arrRqData['end']." 23:59:59' ";    
            }    
            if(strlen($arrRqData['mb_uid'])> 0 ){
                $where .= " AND exchange_mb_uid = '".$arrRqData['mb_uid']."' ";    
            }

            if(array_key_exists('mb_emp_fid', $arrRqData)){
                if(strlen($arrRqData['mb_emp_uid'])> 0 ){
                    $where .= " AND ( exchange_emp_fid = '".$arrRqData['mb_emp_fid']."' OR ";
                    $where .= " exchange_action_uid = '".$arrRqData['mb_emp_uid']."' ) ";
                } else {

                    $where .= " AND exchange_emp_fid = '".$arrRqData['mb_emp_fid']."' ";    
                }
            }     
                    

            $this->mBuilder ->where($where)            
                            ->getCompiledSelect(false);

            return $this->mBuilder->countAllResults();
            
        } catch (\Exception $e) {  
            return 0;
        }
        return 0;
    }

    function searchProcList($arrRqData, $page, $cntPer = 20){
        
        try { 
            
            if($page < 1)
                return NULL;
            if($cntPer < 1)
                return NULL;

            $where = "exchange_state_delete = '0' ";
            if(strlen($arrRqData['start']) > 0){
                $where .= " AND exchange_time_require >= '".$arrRqData['start']."' ";    
            }
            if(strlen($arrRqData['end']) > 0){
                $where .= " AND exchange_time_require <= '".$arrRqData['end']." 23:59:59' ";    
            }    
            if(strlen($arrRqData['mb_uid']) > 0 ){
                $where .= " AND exchange_mb_uid = '".$arrRqData['mb_uid']."' ";    
            }
            if(array_key_exists('mb_emp_fid', $arrRqData)){
                if(strlen($arrRqData['mb_emp_uid'])> 0 ){
                    $where .= " AND ( exchange_emp_fid = '".$arrRqData['mb_emp_fid']."' OR ";
                    $where .= " exchange_action_uid = '".$arrRqData['mb_emp_uid']."' ) ";
                } else {
                    $where .= " AND exchange_emp_fid = '".$arrRqData['mb_emp_fid']."' ";
                }    
            }             

            if(!in_array('mb_nickname', $this->mTbColumn)){
                array_push($this->mTbColumn, 'mb_nickname');
                array_push($this->mTbColumn, 'mb_level');
            }

            $joinTbName = 'member';
            $this->mBuilder ->select($this->mTbColumn)   
                            ->join($joinTbName, $joinTbName.'.mb_uid = '.$this->mTbName.'.exchange_mb_uid')  
                            ->where($where)
                            ->orderBy('exchange_fid', 'DESC')
                            ->getCompiledSelect(false);

            $query = $this->mBuilder->get($cntPer, $cntPer*($page-1));
            return $query->getResult();
           
        } catch (\Exception $e) {  
            return $e;
        }
        return NULL;
        
    }


}