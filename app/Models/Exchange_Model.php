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

    /**
     * Sum Default-type exchange amounts (WAIT+PERMIT) per mb_uid for emp_fid.
     * @return array<string, float>
     */
    public function sumDefaultByEmpFid($emp_fid, $start = '', $end = '')
    {
        $map = [];
        try {
            $where = " exchange_state_delete = '0' AND ";
            $where .= " exchange_type = '".CHARGE_TYPE_DEFAULT."' AND";
            $where .= " exchange_action_state IN ('".CHARGE_STATE_WAIT."','".CHARGE_STATE_PERMIT."') AND";
            $where .= " exchange_emp_fid = '".intval($emp_fid)."' ";
            if (is_string($start) && strlen($start) > 0) {
                $where .= " AND exchange_time_require >= ".$this->mDb->escape($start)." ";
            }
            if (is_string($end) && strlen($end) > 0) {
                $where .= " AND exchange_time_require <= ".$this->mDb->escape($end.' 23:59:59')." ";
            }
            $sql = "SELECT exchange_mb_uid, COALESCE(SUM(exchange_money), 0) AS money_sum
                    FROM ".$this->mTbName."
                    WHERE ".$where."
                    GROUP BY exchange_mb_uid";
            foreach ($this->mDb->query($sql)->getResult() as $row) {
                $uid = (string)$row->exchange_mb_uid;
                if ($uid !== '') {
                    $map[$uid] = (float)$row->money_sum;
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

    /**
     * WAIT → $newState in a single conditional UPDATE so two concurrent
     * confirm/cancel clicks cannot both succeed.
     */
    public function claimWait($exchange_id, $action_uid, $newState)
    {
        $this->mDb->query(
            "UPDATE ".$this->mTbName."
                SET exchange_action_state = ?, exchange_action_uid = ?, exchange_time_process = NOW()
              WHERE exchange_fid = ? AND exchange_action_state = ?",
            [(int)$newState, (string)$action_uid, (int)$exchange_id, CHARGE_STATE_WAIT]
        );
        return $this->mDb->affectedRows() === 1;
    }

    /** Compensation for engines without transactions: restore WAIT if this admin's claim must be undone. */
    public function revertClaim($objExchange, $action_uid, $claimedState)
    {
        $this->mDb->query(
            "UPDATE ".$this->mTbName."
                SET exchange_action_state = ?, exchange_action_uid = ?, exchange_time_process = ?
              WHERE exchange_fid = ? AND exchange_action_state = ? AND exchange_action_uid = ?",
            [
                CHARGE_STATE_WAIT,
                isset($objExchange->exchange_action_uid) ? $objExchange->exchange_action_uid : null,
                isset($objExchange->exchange_time_process) ? $objExchange->exchange_time_process : null,
                (int)$objExchange->exchange_fid,
                (int)$claimedState,
                (string)$action_uid,
            ]
        );
    }

    public function setMoneyAfter($exchange_id, $moneyAfter)
    {
        return $this->mDb->query(
            "UPDATE ".$this->mTbName." SET exchange_money_after = ? WHERE exchange_fid = ?",
            [$moneyAfter, (int)$exchange_id]
        );
    }

    private function procWhere($arrRqData)
    {
        $db = $this->mDb;
        $start = isset($arrRqData['start']) ? trim((string)$arrRqData['start']) : '';
        $end = isset($arrRqData['end']) ? trim((string)$arrRqData['end']) : '';
        $uid = isset($arrRqData['mb_uid']) ? trim((string)$arrRqData['mb_uid']) : '';

        $where = "exchange_state_delete = '0' ";
        if ($start !== '') {
            $where .= " AND exchange_time_require >= ".$db->escape($start)." ";
        }
        if ($end !== '') {
            $where .= " AND exchange_time_require <= ".$db->escape($end.' 23:59:59')." ";
        }
        if ($uid !== '') {
            $where .= " AND exchange_mb_uid = ".$db->escape($uid)." ";
        }
        if (!empty($arrRqData['pending'])) {
            $where .= " AND exchange_action_state = '".CHARGE_STATE_WAIT."' ";
        }
        if (array_key_exists('mb_emp_fid', $arrRqData)) {
            $empFid = (int)$arrRqData['mb_emp_fid'];
            $empUid = isset($arrRqData['mb_emp_uid']) ? (string)$arrRqData['mb_emp_uid'] : '';
            if ($empUid !== '') {
                $where .= " AND ( exchange_emp_fid = '".$empFid."' OR exchange_action_uid = ".$db->escape($empUid)." ) ";
            } else {
                $where .= " AND exchange_emp_fid = '".$empFid."' ";
            }
        }
        return $where;
    }

    private function procBuilder($arrRqData)
    {
        $builder = $this->mDb->table($this->mTbName);
        $builder->where($this->procWhere($arrRqData));
        $channelSql = trim(preg_replace('/^\s*AND\s+/i', '', channel_filter_sql($arrRqData, 'exchange_mb_uid')));
        if ($channelSql !== '') {
            // 서브쿼리가 쿼리빌더 식별자 보호에 깨지지 않도록 escape=false 로 별도 추가
            $builder->where($channelSql, null, false);
        }
        return $builder;
    }

    function searchProcCount($arrRqData){
        try {
            return $this->procBuilder($arrRqData)->countAllResults();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /** Sum of requested amounts over the same filter as searchProcCount (all pages). */
    function searchProcSum($arrRqData){
        try {
            $row = $this->procBuilder($arrRqData)
                ->select('COALESCE(SUM(exchange_money), 0) AS money_sum', false)
                ->get()
                ->getRow();
            return $row ? (float)$row->money_sum : 0.0;
        } catch (\Exception $e) {
            return 0.0;
        }
    }

    function searchProcList($arrRqData, $page, $cntPer = 20){
        
        try { 
            
            if($page < 1)
                return NULL;
            if($cntPer < 1)
                return NULL;

            $columns = $this->mTbColumn;
            $columns[] = 'mb_nickname';
            $columns[] = 'mb_level';

            $joinTbName = 'member';
            $builder = $this->mDb->table($this->mTbName);
            $builder->select($columns)
                    ->join($joinTbName, $joinTbName.'.mb_uid = '.$this->mTbName.'.exchange_mb_uid')
                    ->where($this->procWhere($arrRqData));
            $channelSql = trim(preg_replace('/^\s*AND\s+/i', '', channel_filter_sql($arrRqData, '', $joinTbName)));
            if ($channelSql !== '') {
                $builder->where($channelSql, null, false);
            }
            $query = $builder->orderBy('exchange_fid', 'DESC')
                             ->get($cntPer, $cntPer*($page-1));
            return $query->getResult();
           
        } catch (\Exception $e) {  
            return NULL;
        }
        
    }


}