<?php 
namespace App\Models;

use CodeIgniter\Model;
use Config\Database;

class Charge_Model extends Model {

    private $mDb;
    private $mBuilder;
    private $mTbName = "member_charge";
    private $mTbColumn;

    function __construct()
    {
      
        $this->mDb      = Database::connect();
        $this->mBuilder = $this->mDb->table($this->mTbName);
        $this->mTbColumn = ['charge_fid', 'charge_emp_fid', 'charge_mb_uid', 'charge_mb_name', 'charge_type', 
                    'charge_money', 'charge_time_require', 'charge_action_state', 'charge_time_process', 
                    'charge_money_before', 'charge_money_after'];

    }

    public function getById($charge_id){
        
        try { 
            
            $this->mBuilder->where('charge_fid', $charge_id)
                           ->getCompiledSelect(false);
            $query = $this->mBuilder->get();
            return $query->getRow();
            
        } catch (\Exception $e) {  
            return NULL;
        }
        return NULL;
    }

    
    public function waitCharge($mb_uid){
        
        try { 
            $where = " charge_client_delete = '0' AND ";
            $where.= " charge_type = '".CHARGE_TYPE_DEFAULT."' AND";
            $where.= " charge_action_state = '".CHARGE_STATE_WAIT."' AND";
            $where.= " charge_mb_uid = '".$mb_uid."' ";
            
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

    public function addCharge($objUser, $arrRqData){

    	if(is_null($objUser)) return false;
    	if($arrRqData['amount'] < 1) return false;
    	
    	 //자료기지 등록
    	$this->mBuilder->set('charge_emp_fid', $objUser->mb_emp_fid);
        $this->mBuilder->set('charge_mb_uid', $objUser->mb_uid);
        $this->mBuilder->set('charge_mb_name', $arrRqData['name']);
        $this->mBuilder->set('charge_type', CHARGE_TYPE_DEFAULT);
        $this->mBuilder->set('charge_money', $arrRqData['amount']);
        $this->mBuilder->set('charge_time_require', 'NOW()', false);
        //1:요청, 2:처리완료 3:거절
        $this->mBuilder->set('charge_action_state', CHARGE_STATE_WAIT);
        $this->mBuilder->set('charge_money_before', $objUser->mb_money);
		
        return $this->mBuilder->insert();
        
    }

    
    //서비스충전
    public function addPresent($objUser, $objAdmin, $arrRqData){

    	if(is_null($objUser)) return false;
    	if($arrRqData['money'] < 1) return false;
    	
        $nMoney = intval($arrRqData['money']);

    	 //자료기지 등록
    	$this->mBuilder->set('charge_emp_fid', $objUser->mb_emp_fid);
        $this->mBuilder->set('charge_mb_uid', $objUser->mb_uid);
        $this->mBuilder->set('charge_mb_name', $objUser->mb_nickname);
        $this->mBuilder->set('charge_type', CHARGE_TYPE_PRESENT);
        $this->mBuilder->set('charge_money', $nMoney);
        $this->mBuilder->set('charge_time_require', 'NOW()', false);
        //1:요청, 2:처리완료 3:거절
        $this->mBuilder->set('charge_action_uid', $objAdmin->mb_uid);
        $this->mBuilder->set('charge_action_state', CHARGE_STATE_PERMIT);
        $this->mBuilder->set('charge_money_before', $objUser->mb_money);
        $this->mBuilder->set('charge_money_after', $objUser->mb_money +  $nMoney);
		$this->mBuilder->set('charge_time_process', 'NOW()', false);

        return $this->mBuilder->insert();
        
    }

    public function procCharge($objCharge, $objUser, $actionState){

    	if(is_null($objUser)) return false;
    	
        $this->mBuilder->set('charge_action_uid', $objUser->mb_uid);        
        $this->mBuilder->set('charge_action_state', $actionState);
        if($actionState == CHARGE_STATE_PERMIT)
            $this->mBuilder->set('charge_money_after', $objUser->mb_money + $objCharge->charge_money);
		$this->mBuilder->set('charge_time_process', 'NOW()', false);

        $this->mBuilder->where('charge_fid', $objCharge->charge_fid);

        return $this->mBuilder->update();   //if success, return true
        
    }
    

    public function waitProc($emp_fid){

    	try { 
            $where = " charge_state_delete = '0' AND ";
            $where.= " charge_type = '".CHARGE_TYPE_DEFAULT."' AND";
            $where.= " charge_action_state = '".CHARGE_STATE_WAIT."' AND";
            $where.= " charge_emp_fid = '".$emp_fid."' ";
            
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
     * Pending Default-type charges for emp_fid, keyed by charge_mb_uid (latest per uid).
     * @return array<string, object>
     */
    public function mapWaitDefaultByEmpFid($emp_fid)
    {
        $map = [];
        try {
            $where = " charge_state_delete = '0' AND ";
            $where .= " charge_type = '".CHARGE_TYPE_DEFAULT."' AND";
            $where .= " charge_action_state = '".CHARGE_STATE_WAIT."' AND";
            $where .= " charge_emp_fid = '".intval($emp_fid)."' ";
            $rows = $this->mBuilder->select($this->mTbColumn)
                ->where($where)
                ->orderBy('charge_fid', 'DESC')
                ->get()
                ->getResult();
            foreach ($rows as $row) {
                $uid = (string)$row->charge_mb_uid;
                if ($uid !== '' && !isset($map[$uid])) {
                    $map[$uid] = $row;
                }
            }
        } catch (\Exception $e) {
            return [];
        }
        return $map;
    }

    public function deleteCharge($charge_id){

        $this->mBuilder->set('charge_client_delete', '1');

        $this->mBuilder->where('charge_fid', $charge_id);

        return $this->mBuilder->update();   //if success, return true
    }

    public function deleteChargeProc($charge_id){

        $this->mBuilder->set('charge_state_delete', '1');

        $this->mBuilder->where('charge_fid', $charge_id);

        return $this->mBuilder->update();   //if success, return true
    }

    function searchCount($arrRqData){
        
        try { 
            
            $where = "charge_client_delete = '0' ";
            $where.= "AND charge_type = '".CHARGE_TYPE_DEFAULT."' ";
            if(array_key_exists('mb_uid', $arrRqData)){
                $where .= " AND charge_mb_uid = '".$arrRqData['mb_uid']."' ";    
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

            $where = "charge_client_delete = '0' ";
            $where.= "AND charge_type = '".CHARGE_TYPE_DEFAULT."' ";
            if(array_key_exists('mb_uid', $arrRqData)){
                $where .= " AND charge_mb_uid = '".$arrRqData['mb_uid']."' ";    
            }             

            $this->mBuilder ->select($this->mTbColumn)     
                            ->where($where)
                            ->orderBy('charge_fid', 'DESC')
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
            
            $where = "charge_state_delete = '0' ";
            if(strlen($arrRqData['start']) > 0){
                $where .= " AND charge_time_require >= '".$arrRqData['start']."' ";    
            }
            if(strlen($arrRqData['end']) > 0){
                $where .= " AND charge_time_require <= '".$arrRqData['end']." 23:59:59' ";    
            }    
            if(strlen($arrRqData['mb_uid'])> 0 ){
                $where .= " AND charge_mb_uid = '".$arrRqData['mb_uid']."' ";    
            }

            if(array_key_exists('mb_emp_fid', $arrRqData)){

                if(strlen($arrRqData['mb_emp_uid'])> 0 ){
                    $where .= " AND ( charge_emp_fid = '".$arrRqData['mb_emp_fid']."' OR ";
                    $where .= " charge_action_uid = '".$arrRqData['mb_emp_uid']."' ) ";
                } else {
                    $where .= " AND charge_emp_fid = '".$arrRqData['mb_emp_fid']."' ";    
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

            $where = "charge_state_delete = '0' ";
            if(strlen($arrRqData['start']) > 0){
                $where .= " AND charge_time_require >= '".$arrRqData['start']."' ";    
            }
            if(strlen($arrRqData['end']) > 0){
                $where .= " AND charge_time_require <= '".$arrRqData['end']." 23:59:59' ";    
            }    
            if(strlen($arrRqData['mb_uid']) > 0 ){
                $where .= " AND charge_mb_uid = '".$arrRqData['mb_uid']."' ";    
            }
            if(array_key_exists('mb_emp_fid', $arrRqData)){
                if(strlen($arrRqData['mb_emp_uid'])> 0 ){
                    $where .= " AND ( charge_emp_fid = '".$arrRqData['mb_emp_fid']."' OR ";
                    $where .= " charge_action_uid = '".$arrRqData['mb_emp_uid']."' ) ";
                } else {
                    $where .= " AND charge_emp_fid = '".$arrRqData['mb_emp_fid']."' ";    
                }
            }             

            if(!in_array('mb_nickname', $this->mTbColumn)){
                array_push($this->mTbColumn, 'mb_nickname');
                array_push($this->mTbColumn, 'mb_level');
            }

            $joinTbName = 'member';
            $this->mBuilder ->select($this->mTbColumn)   
                            ->join($joinTbName, $joinTbName.'.mb_uid = '.$this->mTbName.'.charge_mb_uid')  
                            ->where($where)
                            ->orderBy('charge_fid', 'DESC')
                            ->getCompiledSelect(false);

            $query = $this->mBuilder->get($cntPer, $cntPer*($page-1));
            return $query->getResult();
           
        } catch (\Exception $e) {  
            return $e;
        }
        return NULL;
        
    }



}