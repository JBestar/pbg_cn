<?php 
namespace App\Models;

use CodeIgniter\Model;
use Config\Database;

class MoneyHist_Model extends Model {

    private $mDb;
    private $mBuilder;
    private $mTbName = "money_history";
    private $mTbColumn;

    function __construct()
    {      
        $this->mDb      = Database::connect();
        $this->mBuilder = $this->mDb->table($this->mTbName);

        $this->mTbColumn = ['money_fid', 'money_mb_fid', 'money_mb_uid', 'money_mb_emp_fid', 'money_mb_ech_uid',
            'money_amount', 'money_before', 'money_after', 'money_change_type', 'money_mb_fid', 
            'money_update_time'];
    }

    
    function registerMoneyBet($objMember, $arrBetData)
    {
        $money_amount = 0 - $arrBetData['amount'];
        
        $this->mBuilder->set('money_mb_fid', $objMember->mb_fid);
        $this->mBuilder->set('money_mb_uid', $objMember->mb_uid);
        $this->mBuilder->set('money_mb_emp_fid', $objMember->mb_emp_fid);
        $this->mBuilder->set('money_amount', $money_amount);
        $this->mBuilder->set('money_before', $objMember->mb_money);
        $this->mBuilder->set('money_after', $objMember->mb_money + $money_amount );
        $this->mBuilder->set('money_change_type', MONEYCHANGE_BET); 

        $this->mBuilder->set('money_bet_round', $arrBetData['round_id']);
        $this->mBuilder->set('money_bet_mode', $arrBetData['mode']);
        $this->mBuilder->set('money_update_time', 'NOW()', false);
        
        return $this->mBuilder->insert();

    }

    function registerMoneyAcc($objMember, $objBetInfo)
    {
        $money_amount = $objBetInfo->bet_win_money;
        
        $this->mBuilder->set('money_mb_fid', $objMember->mb_fid);
        $this->mBuilder->set('money_mb_uid', $objMember->mb_uid);
        $this->mBuilder->set('money_mb_emp_fid', $objMember->mb_emp_fid);
        $this->mBuilder->set('money_amount', $money_amount);
        $this->mBuilder->set('money_before', $objMember->mb_money);
        $this->mBuilder->set('money_after', $objMember->mb_money + $money_amount );
        $this->mBuilder->set('money_change_type', MONEYCHANGE_WIN); 

        $this->mBuilder->set('money_bet_round', $objBetInfo->bet_round_fid);
        $this->mBuilder->set('money_bet_mode', $objBetInfo->bet_mode);
        $this->mBuilder->set('money_update_time', $objBetInfo->bet_account_time);
        
        return $this->mBuilder->insert();

    }

    function registerMoneyCancel($objMember, $nMoney)
    {
                
        $this->mBuilder->set('money_mb_fid', $objMember->mb_fid);
        $this->mBuilder->set('money_mb_uid', $objMember->mb_uid);
        $this->mBuilder->set('money_mb_emp_fid', $objMember->mb_emp_fid);
        $this->mBuilder->set('money_amount', $nMoney);
        $this->mBuilder->set('money_before', $objMember->mb_money);
        $this->mBuilder->set('money_after', $objMember->mb_money + $nMoney );
        $this->mBuilder->set('money_change_type', MONEYCHANGE_CANCEL); 

        $this->mBuilder->set('money_update_time', 'NOW()', false);
        
        return $this->mBuilder->insert();

    }

    function registerPointBetOrCancel($objMember, $nPoint, $bBet)
    {        
        if($nPoint == 0)
            return true;

        $nPoint = $bBet ? $nPoint: 0 - $nPoint;

        $this->mBuilder->set('money_mb_fid', $objMember->mb_fid);
        $this->mBuilder->set('money_mb_uid', $objMember->mb_uid);
        $this->mBuilder->set('money_mb_emp_fid', $objMember->mb_emp_fid);
        $this->mBuilder->set('money_amount', $nPoint);
        $this->mBuilder->set('money_before', $objMember->mb_point);
        $this->mBuilder->set('money_after', $objMember->mb_point + $nPoint );
        $this->mBuilder->set('money_change_type', $bBet? POINTCHANGE_BET : POINTCHANGE_CANCEL); 

        $this->mBuilder->set('money_update_time', 'NOW()', false);
        
        return $this->mBuilder->insert();
    }

    function registerPointToMoney($objMember, $nPoint)
    {
                
        $this->mBuilder->set('money_mb_fid', $objMember->mb_fid);
        $this->mBuilder->set('money_mb_uid', $objMember->mb_uid);
        $this->mBuilder->set('money_mb_emp_fid', $objMember->mb_emp_fid);
        $this->mBuilder->set('money_amount', $nPoint);
        $this->mBuilder->set('money_before', $objMember->mb_money);
        $this->mBuilder->set('money_after', $objMember->mb_money + $nPoint );
        $this->mBuilder->set('money_change_type', POINTCHANGE_EXCHANGE); 

        $this->mBuilder->set('money_update_time', 'NOW()', false);
        
        return $this->mBuilder->insert();

    }


    function registerCharge($objMember, $nMoney)
    {
        if($nMoney == 0)
            return true;
        if($nMoney < 0)
            $nMoney = 0 - $nMoney;

        $this->mBuilder->set('money_mb_fid', $objMember->mb_fid);
        $this->mBuilder->set('money_mb_uid', $objMember->mb_uid);
        $this->mBuilder->set('money_mb_emp_fid', $objMember->mb_emp_fid);
        if(isset($objMember->mb_ech_uid))
            $this->mBuilder->set('money_mb_ech_uid', $objMember->mb_ech_uid); 
        $this->mBuilder->set('money_amount', $nMoney);
        $this->mBuilder->set('money_before', $objMember->mb_money);
        $this->mBuilder->set('money_after', $objMember->mb_money + $nMoney );
        $this->mBuilder->set('money_change_type', MONEYCHANGE_CHARGE); 

        $this->mBuilder->set('money_update_time', 'NOW()', false);
        
        return $this->mBuilder->insert();

    }

    function registerChargeFrom($objMember, $nMoney)
    {
        if($nMoney == 0)
            return true;
        if($nMoney > 0)
            $nMoney = 0 - $nMoney;

        $this->mBuilder->set('money_mb_fid', $objMember->mb_fid);
        $this->mBuilder->set('money_mb_uid', $objMember->mb_uid);
        $this->mBuilder->set('money_mb_emp_fid', $objMember->mb_emp_fid);
        if(isset($objMember->mb_ech_uid))
            $this->mBuilder->set('money_mb_ech_uid', $objMember->mb_ech_uid); 
        $this->mBuilder->set('money_amount', $nMoney);
        $this->mBuilder->set('money_before', $objMember->mb_money);
        $this->mBuilder->set('money_after', $objMember->mb_money + $nMoney );
        $this->mBuilder->set('money_change_type', MONEYCHANGE_CHARGE_FROM); 

        $this->mBuilder->set('money_update_time', 'NOW()', false);
        
        return $this->mBuilder->insert();

    }

    function registerExchange($objMember, $nMoney)
    {
        if($nMoney == 0)
            return true;
        if($nMoney > 0)
            $nMoney = 0 - $nMoney;

        $this->mBuilder->set('money_mb_fid', $objMember->mb_fid);
        $this->mBuilder->set('money_mb_uid', $objMember->mb_uid);
        $this->mBuilder->set('money_mb_emp_fid', $objMember->mb_emp_fid);
        if(isset($objMember->mb_ech_uid))
            $this->mBuilder->set('money_mb_ech_uid', $objMember->mb_ech_uid); 
        $this->mBuilder->set('money_amount', $nMoney);
        $this->mBuilder->set('money_before', $objMember->mb_money);
        $this->mBuilder->set('money_after', $objMember->mb_money + $nMoney );
        $this->mBuilder->set('money_change_type', MONEYCHANGE_EXCHANGE); 

        $this->mBuilder->set('money_update_time', 'NOW()', false);
        
        return $this->mBuilder->insert();

    }

    function registerExchangeTo($objMember, $nMoney)
    {
        if($nMoney == 0)
            return true;
        if($nMoney < 0)
            $nMoney = 0 - $nMoney;

        $this->mBuilder->set('money_mb_fid', $objMember->mb_fid);
        $this->mBuilder->set('money_mb_uid', $objMember->mb_uid);
        $this->mBuilder->set('money_mb_emp_fid', $objMember->mb_emp_fid);
        if(isset($objMember->mb_ech_uid))
            $this->mBuilder->set('money_mb_ech_uid', $objMember->mb_ech_uid); 
        $this->mBuilder->set('money_amount', $nMoney);
        $this->mBuilder->set('money_before', $objMember->mb_money);
        $this->mBuilder->set('money_after', $objMember->mb_money + $nMoney );
        $this->mBuilder->set('money_change_type', MONEYCHANGE_EXCHANGE_TO); 

        $this->mBuilder->set('money_update_time', 'NOW()', false);
        
        return $this->mBuilder->insert();

    }

    //서비스 충전
    function registerPresent($objMember, $nMoney)
    {
        if($nMoney == 0)
            return true;
        if($nMoney < 0)
            $nMoney = 0 - $nMoney;

        $this->mBuilder->set('money_mb_fid', $objMember->mb_fid);
        $this->mBuilder->set('money_mb_uid', $objMember->mb_uid);
        $this->mBuilder->set('money_mb_emp_fid', $objMember->mb_emp_fid);
        if(isset($objMember->mb_ech_uid))
            $this->mBuilder->set('money_mb_ech_uid', $objMember->mb_ech_uid); 
        $this->mBuilder->set('money_amount', $nMoney);
        $this->mBuilder->set('money_before', $objMember->mb_money);
        $this->mBuilder->set('money_after', $objMember->mb_money + $nMoney );
        $this->mBuilder->set('money_change_type', MONEYCHANGE_PRESENT); 

        $this->mBuilder->set('money_update_time', 'NOW()', false);
        
        return $this->mBuilder->insert();

    }

    function registerPresentFrom($objMember, $nMoney)
    {
        if($nMoney == 0)
            return true;
        if($nMoney > 0)
            $nMoney = 0 - $nMoney;

        $this->mBuilder->set('money_mb_fid', $objMember->mb_fid);
        $this->mBuilder->set('money_mb_uid', $objMember->mb_uid);
        $this->mBuilder->set('money_mb_emp_fid', $objMember->mb_emp_fid);
        if(isset($objMember->mb_ech_uid))
            $this->mBuilder->set('money_mb_ech_uid', $objMember->mb_ech_uid); 
        $this->mBuilder->set('money_amount', $nMoney);
        $this->mBuilder->set('money_before', $objMember->mb_money);
        $this->mBuilder->set('money_after', $objMember->mb_money + $nMoney );
        $this->mBuilder->set('money_change_type', MONEYCHANGE_PRESENT_FROM); 

        $this->mBuilder->set('money_update_time', 'NOW()', false);
        
        return $this->mBuilder->insert();

    }



    //머니 회수
    function registerRecovery($objMember, $nMoney)
    {
        if($nMoney == 0)
            return true;
        if($nMoney > 0)
            $nMoney = 0 - $nMoney;

        $this->mBuilder->set('money_mb_fid', $objMember->mb_fid);
        $this->mBuilder->set('money_mb_uid', $objMember->mb_uid);
        $this->mBuilder->set('money_mb_emp_fid', $objMember->mb_emp_fid);
        if(isset($objMember->mb_ech_uid))
            $this->mBuilder->set('money_mb_ech_uid', $objMember->mb_ech_uid); 
        $this->mBuilder->set('money_amount', $nMoney);
        $this->mBuilder->set('money_before', $objMember->mb_money);
        $this->mBuilder->set('money_after', $objMember->mb_money + $nMoney );
        $this->mBuilder->set('money_change_type', MONEYCHANGE_RECOVERY); 

        $this->mBuilder->set('money_update_time', 'NOW()', false);
        
        return $this->mBuilder->insert();

    }

    function registerRecoveryTo($objMember, $nMoney)
    {
        if($nMoney == 0)
            return true;
        if($nMoney < 0)
            $nMoney = 0 - $nMoney;

        $this->mBuilder->set('money_mb_fid', $objMember->mb_fid);
        $this->mBuilder->set('money_mb_uid', $objMember->mb_uid);
        $this->mBuilder->set('money_mb_emp_fid', $objMember->mb_emp_fid);
        if(isset($objMember->mb_ech_uid))
            $this->mBuilder->set('money_mb_ech_uid', $objMember->mb_ech_uid); 
        $this->mBuilder->set('money_amount', $nMoney);
        $this->mBuilder->set('money_before', $objMember->mb_money);
        $this->mBuilder->set('money_after', $objMember->mb_money + $nMoney );
        $this->mBuilder->set('money_change_type', MONEYCHANGE_RECOVERY_TO); 

        $this->mBuilder->set('money_update_time', 'NOW()', false);
        
        return $this->mBuilder->insert();

    }


    
    function searchCount($arrRqData){
        
        try { 
            
            $where = "money_fid > 0 ";
            if( array_key_exists('start', $arrRqData) && strlen($arrRqData['start']) > 0 ){
                $where .= " AND money_update_time >= '".$arrRqData['start']."' ";    
            }
            if(array_key_exists('end', $arrRqData) && strlen($arrRqData['end']) > 0){
                $where .= " AND money_update_time <= '".$arrRqData['end']." 23:59:59' ";    
            }            
            if(array_key_exists('mb_uid', $arrRqData)){
                $where .= " AND money_mb_uid = '".$arrRqData['mb_uid']."' ";    
            }
            if(array_key_exists('type', $arrRqData) && intval($arrRqData['type']) > 0){
                $where .= " AND money_change_type = '".$arrRqData['type']."' ";    
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

            $where = "money_fid > 0 ";
            if( array_key_exists('start', $arrRqData) && strlen($arrRqData['start']) > 0 ){
                $where .= " AND money_update_time >= '".$arrRqData['start']."' ";    
            }
            if(array_key_exists('end', $arrRqData) && strlen($arrRqData['end']) > 0){
                $where .= " AND money_update_time <= '".$arrRqData['end']." 23:59:59' ";    
            }            
            if(array_key_exists('mb_uid', $arrRqData)){
                $where .= " AND money_mb_uid = '".$arrRqData['mb_uid']."' ";    
            }
            if(array_key_exists('type', $arrRqData) && intval($arrRqData['type']) > 0){
                $where .= " AND money_change_type = '".$arrRqData['type']."' ";    
            } 

            if(!in_array('mb_nickname', $this->mTbColumn)){
                array_push($this->mTbColumn, 'mb_nickname');                
            }                

            $joinTbName = 'member';
            $this->mBuilder ->select($this->mTbColumn)     
                            ->join($joinTbName, $joinTbName.'.mb_fid = '.$this->mTbName.'.money_mb_fid', 'right')
                            ->where($where)
                            ->orderBy('money_fid', 'DESC')
                            ->getCompiledSelect(false);

            $query = $this->mBuilder->get($cntPer, $cntPer*($page-1));
            return $query->getResult();
            
        } catch (\Exception $e) {  
            return NULL;
        }
        return NULL;
    }



    function getAccList($arrRqData)
    {  
        $arrAcc = [];
        $startDate = $arrRqData['start'];
        $endDate = $arrRqData['end'];

        if(strlen($startDate) < 1)
            $startDate = date('Y-m-d');
        if(strlen($endDate) < 1)
            $endDate = date('Y-m-d');

        $period = new \DatePeriod( new \DateTime($startDate), new \DateInterval('P1D'), new \DateTime($endDate." +1 day"));
        
        
        foreach ($period as $date) {
            $sDate = $date->format("Y-m-d");
            $objAcc = new \StdClass;
            $objAcc->date = $sDate;
            $objAcc->money_charge = 0;
            $objAcc->money_exchange = 0;
            $objAcc->money_give = 0;
            $objAcc->money_recovery = 0;
            $objAcc->money_bet = 0;
            $objAcc->money_win = 0;
            $objAcc->point_empl = 0;
            $objAcc->point_agen = 0;

            $arrAcc[$sDate] = $objAcc;
        }

        $where = "money_change_type >= '1' AND money_change_type <= '4' ";
        $where .= " AND money_update_time >= '".$startDate."' ";    
        $where .= " AND money_update_time <= '".$endDate." 23:59:59' ";    
        
        if(array_key_exists('mb_uid', $arrRqData) && strlen($arrRqData['mb_uid']) > 0){
            $where .= " AND money_mb_uid = '".$arrRqData['mb_uid']."' ";    
        }
        if(array_key_exists('mb_emp_fid', $arrRqData)){
            $where .= " AND money_mb_emp_fid = '".$arrRqData['mb_emp_fid']."' ";    
        }

        //회차한도
        $strSql = " SELECT CAST(money_update_time AS DATE) AS money_update_date, money_change_type ";
        //$strSql.= ", money_mb_emp_fid ";
        $strSql.= ", SUM(money_amount) AS money_sum FROM ".$this->mTbName;
        $strSql.= " WHERE ".$where;
        $strSql.= " GROUP BY CAST(money_update_time AS DATE), money_change_type";
        //$strSql.= ", money_mb_emp_fid ";

        $arrResult = $this->mDb->query($strSql)->getResult();

        foreach ($arrResult as $objResult) {
            if(!array_key_exists($objResult->money_update_date, $arrAcc))
                continue;
            switch(intval($objResult->money_change_type)){
                case MONEYCHANGE_CHARGE:
                    $arrAcc[$objResult->money_update_date]->money_charge += $objResult->money_sum;
                    break;
                case MONEYCHANGE_EXCHANGE:
                    $arrAcc[$objResult->money_update_date]->money_exchange += $objResult->money_sum;
                    break;
                case MONEYCHANGE_PRESENT:
                    $arrAcc[$objResult->money_update_date]->money_give += $objResult->money_sum;
                    break;
                case MONEYCHANGE_RECOVERY:
                    $arrAcc[$objResult->money_update_date]->money_recovery += $objResult->money_sum;
                    break;
                /*
                case MONEYCHANGE_BET:
                    $arrAcc[$objResult->money_update_date]->money_bet += $objResult->money_sum;
                    break;
                case MONEYCHANGE_WIN:
                    $arrAcc[$objResult->money_update_date]->money_win += $objResult->money_sum;
                    break;
                case MONEYCHANGE_CANCEL:
                    $arrAcc[$objResult->money_update_date]->money_win += $objResult->money_sum;
                    break;
                case POINTCHANGE_BET:
                    $arrAcc[$objResult->money_update_date]->point_bet += $objResult->money_sum;
                    break;
                case POINTCHANGE_CANCEL:
                    $arrAcc[$objResult->money_update_date]->point_bet += $objResult->money_sum;
                    break;
                case POINTCHANGE_EXCHANGE:
                    break;
                */
            }
        }

        
        $where = " ( state = 2 OR state = 3 ) AND created_at >= '".$startDate."' ";    
        $where .= " AND created_at <= '".$endDate." 23:59:59' ";    
        
        if(array_key_exists('mb_uid', $arrRqData) && strlen($arrRqData['mb_uid']) > 0){
            $where .= " AND mb_uid = '".$this->mDb->escapeString($arrRqData['mb_uid'])."' ";    
        }
        if(array_key_exists('mb_emp_fid', $arrRqData)){
            $where .= " AND emp_fid = '".intval($arrRqData['mb_emp_fid'])."' ";    
        }

        try {
            $strSql = " SELECT CAST(created_at AS DATE) AS bet_date, SUM(amount) AS bet_sum , SUM(win_amount) AS win_sum,
                    COALESCE(SUM(empl_point), 0) AS empl_sum, COALESCE(SUM(agen_point), 0) AS agen_sum ";
            $strSql.= " FROM bets ";
            $strSql.= " WHERE ".$where;
            $strSql.= " GROUP BY CAST(created_at AS DATE)";

            $arrResult = $this->mDb->query($strSql)->getResult();

            foreach ($arrResult as $objResult) {
                if(!array_key_exists($objResult->bet_date, $arrAcc))
                    continue;
                $arrAcc[$objResult->bet_date]->money_bet += $objResult->bet_sum;
                $arrAcc[$objResult->bet_date]->money_win += $objResult->win_sum;
                $arrAcc[$objResult->bet_date]->point_empl += (float)$objResult->empl_sum;
                $arrAcc[$objResult->bet_date]->point_agen += (float)$objResult->agen_sum;
            }
        } catch (\Exception $e) {
            // bets 집계 실패 시 날짜 뼈대만 유지
        }

        return $arrAcc;
    }



    function getAccRange($arrRqData)
    {  
        
        $objAcc = new \StdClass;
        $objAcc->money_charge = 0;
        $objAcc->money_exchange = 0;
        $objAcc->money_give = 0;
        $objAcc->money_recovery = 0;
        $objAcc->money_bet = 0;
        $objAcc->money_win = 0;
        $objAcc->point_empl = 0;
        $objAcc->point_agen = 0;
        $objAcc->point_bet = 0;
        
        $startDate = $arrRqData['start'];
        $endDate = $arrRqData['end'];
        
        $where = " money_update_time >= '".$startDate."' ";    
        $where .= " AND money_update_time <= '".$endDate." 23:59:59' ";    
        
        if(array_key_exists('mb_uid', $arrRqData) && strlen($arrRqData['mb_uid']) > 0){
            $where .= " AND money_mb_uid = '".$arrRqData['mb_uid']."' ";    
        }
        if(array_key_exists('mb_emp_fid', $arrRqData)){
            $where .= " AND money_mb_emp_fid = '".$arrRqData['mb_emp_fid']."' ";    
        }

        $strSql = " SELECT money_change_type, ";
        $strSql.= " SUM(money_amount) AS money_sum FROM ".$this->mTbName;
        $strSql.= " WHERE ".$where;
        $strSql.= " GROUP BY money_change_type";

        $arrResult = $this->mDb->query($strSql)->getResult();

        foreach ($arrResult as $objResult) {
            
            switch(intval($objResult->money_change_type)){
                case MONEYCHANGE_CHARGE:
                    $objAcc->money_charge += $objResult->money_sum;
                    break;
                case MONEYCHANGE_EXCHANGE:
                    $objAcc->money_exchange += $objResult->money_sum;
                    break;
                case MONEYCHANGE_PRESENT:
                    $objAcc->money_give += $objResult->money_sum;
                    break;
                case MONEYCHANGE_RECOVERY:
                    $objAcc->money_recovery += $objResult->money_sum;
                    break;
                case MONEYCHANGE_BET:
                    $objAcc->money_bet += $objResult->money_sum;
                    break;
                case MONEYCHANGE_WIN:
                    $objAcc->money_win += $objResult->money_sum;
                    break;
                case MONEYCHANGE_CANCEL:
                    $objAcc->money_win += $objResult->money_sum;
                    break;
                case POINTCHANGE_BET:
                    $objAcc->point_bet += $objResult->money_sum;
                    break;
                case POINTCHANGE_CANCEL:
                    $objAcc->point_bet += $objResult->money_sum;
                    break;
                case POINTCHANGE_EXCHANGE:
                    break;
                    
            }
        }

        // bets 월간/기간 배팅·당첨
        try {
            $whereBet = " ( state = 2 OR state = 3 ) AND created_at >= '".$startDate."' ";
            $whereBet .= " AND created_at <= '".$endDate." 23:59:59' ";
            if (array_key_exists('mb_uid', $arrRqData) && strlen($arrRqData['mb_uid']) > 0) {
                $whereBet .= " AND mb_uid = '".$this->mDb->escapeString($arrRqData['mb_uid'])."' ";
            }
            if (array_key_exists('mb_emp_fid', $arrRqData)) {
                $whereBet .= " AND emp_fid = '".intval($arrRqData['mb_emp_fid'])."' ";
            }
            $strSql = " SELECT COALESCE(SUM(amount),0) AS bet_sum, COALESCE(SUM(win_amount),0) AS win_sum FROM bets WHERE ".$whereBet;
            $row = $this->mDb->query($strSql)->getRow();
            if ($row) {
                $objAcc->money_bet += (float)$row->bet_sum;
                $objAcc->money_win += (float)$row->win_sum;
            }
        } catch (\Exception $e) {
        }

        return $objAcc;
    }

    /**
     * 매장/총판 충환전 집계
     * $scope: 'store' | 'agency'
     */
    /**
     * CE type sets. hq_ce: 본사↔총판 알충전/회수 + 포인트전환(머니)
     */
    private function ceTypeSets($scope, $hqCe = false)
    {
        if ($scope === 'agency' && $hqCe) {
            return [
                'types' => [MONEYCHANGE_PRESENT, MONEYCHANGE_RECOVERY, POINTCHANGE_EXCHANGE],
                'charge' => [MONEYCHANGE_PRESENT],
                'exchange' => [MONEYCHANGE_RECOVERY],
                'point' => [POINTCHANGE_EXCHANGE],
            ];
        }
        if ($scope === 'agency') {
            return [
                'types' => [MONEYCHANGE_CHARGE, MONEYCHANGE_EXCHANGE, MONEYCHANGE_PRESENT, MONEYCHANGE_RECOVERY, POINTCHANGE_EXCHANGE],
                'charge' => [MONEYCHANGE_CHARGE, MONEYCHANGE_PRESENT],
                'exchange' => [MONEYCHANGE_EXCHANGE, MONEYCHANGE_RECOVERY],
                'point' => [POINTCHANGE_EXCHANGE],
            ];
        }
        return [
            'types' => [MONEYCHANGE_PRESENT, MONEYCHANGE_RECOVERY, POINTCHANGE_EXCHANGE],
            'charge' => [MONEYCHANGE_PRESENT],
            'exchange' => [MONEYCHANGE_RECOVERY],
            'point' => [POINTCHANGE_EXCHANGE],
        ];
    }

    function getCeSummary($arrRqData, $scope = 'store')
    {
        $level = ($scope === 'agency') ? LEVEL_AGENCY : LEVEL_EMPLOYEE;
        $hqCe = !empty($arrRqData['hq_ce']);
        $sets = $this->ceTypeSets($scope, $hqCe);
        $types = $sets['types'];
        $chargeTypes = $sets['charge'];
        $exchangeTypes = $sets['exchange'];
        $pointTypes = $sets['point'];

        $whereMember = " m.mb_level = '".$level."' AND m.mb_state_delete = '0' ";
        if (array_key_exists('mb_emp_fid', $arrRqData) && intval($arrRqData['mb_emp_fid']) > 0) {
            $whereMember .= " AND m.mb_emp_fid = '".intval($arrRqData['mb_emp_fid'])."' ";
        }
        if (array_key_exists('self_uid', $arrRqData) && strlen($arrRqData['self_uid']) > 0) {
            $whereMember .= " AND m.mb_uid = ".$this->mDb->escape($arrRqData['self_uid'])." ";
        }
        if (array_key_exists('mb_uid', $arrRqData) && strlen(trim($arrRqData['mb_uid'])) > 0) {
            $whereMember .= " AND m.mb_uid LIKE ".$this->mDb->escape('%'.trim($arrRqData['mb_uid']).'%')." ";
        }

        $whereHist = "1=1";
        if (array_key_exists('start', $arrRqData) && strlen($arrRqData['start']) > 0) {
            $whereHist .= " AND h.money_update_time >= ".$this->mDb->escape($arrRqData['start'])." ";
        }
        if (array_key_exists('end', $arrRqData) && strlen($arrRqData['end']) > 0) {
            $whereHist .= " AND h.money_update_time <= ".$this->mDb->escape($arrRqData['end'].' 23:59:59')." ";
        }
        $typeIn = implode(',', array_map('intval', $types));
        $whereHist .= " AND h.money_change_type IN (".$typeIn.") ";

        $chargeCase = "CASE WHEN h.money_change_type IN (".implode(',', array_map('intval', $chargeTypes)).") THEN ABS(h.money_amount) ELSE 0 END";
        $exchangeCase = "CASE WHEN h.money_change_type IN (".implode(',', array_map('intval', $exchangeTypes)).") THEN ABS(h.money_amount) ELSE 0 END";
        $pointCase = "CASE WHEN h.money_change_type IN (".implode(',', array_map('intval', $pointTypes)).") THEN ABS(h.money_amount) ELSE 0 END";

        $joinAgency = ($scope === 'store');
        $strSql = " SELECT m.mb_uid, m.mb_nickname, m.mb_level, ";
        if ($joinAgency) {
            $strSql.= " agen.mb_uid AS agency_uid, ";
        }
        $strSql.= " COALESCE(SUM(".$chargeCase."), 0) AS charge_sum, ";
        $strSql.= " COALESCE(SUM(".$exchangeCase."), 0) AS exchange_sum, ";
        $strSql.= " COALESCE(SUM(".$pointCase."), 0) AS point_sum ";
        $strSql.= " FROM member m ";
        if ($joinAgency) {
            $strSql.= " LEFT JOIN member agen ON agen.mb_fid = m.mb_emp_fid ";
        }
        $strSql.= " LEFT JOIN ".$this->mTbName." h ON h.money_mb_uid = m.mb_uid AND ".$whereHist;
        $strSql.= " WHERE ".$whereMember;
        $strSql.= " GROUP BY m.mb_uid, m.mb_nickname, m.mb_level ";
        if ($joinAgency) {
            $strSql.= ", agen.mb_uid ";
        }
        $strSql.= " ORDER BY ";
        if ($joinAgency) {
            $strSql.= " agen.mb_uid ASC, ";
        }
        $strSql.= " m.mb_uid ASC ";

        $rows = $this->mDb->query($strSql)->getResult();
        $out = [];
        foreach ($rows as $row) {
            $charge = intval($row->charge_sum);
            $exchange = intval($row->exchange_sum);
            $point = round((float)$row->point_sum, 2);
            $obj = new \StdClass;
            $obj->mb_uid = $row->mb_uid;
            $obj->mb_nickname = $row->mb_nickname;
            $obj->mb_level = intval($row->mb_level);
            if ($joinAgency) {
                $obj->agency_uid = isset($row->agency_uid) ? $row->agency_uid : '';
            }
            $obj->charge_sum = $charge;
            $obj->exchange_sum = $exchange;
            $obj->point_sum = $point;
            $obj->diff_sum = round($charge - $exchange - $point, 2);
            $out[] = $obj;
        }
        return $out;
    }

    /**
     * 아이디별 충환전 상세
     * $scope: 'store' | 'agency'
     */
    function getCeDetail($arrRqData, $scope = 'store')
    {
        $hqCe = !empty($arrRqData['hq_ce']);
        $sets = $this->ceTypeSets($scope, $hqCe);
        $types = $sets['types'];
        $chargeTypes = $sets['charge'];
        $exchangeTypes = $sets['exchange'];
        $pointTypes = $sets['point'];

        $where = " h.money_mb_uid = ".$this->mDb->escape($arrRqData['mb_uid'])." ";
        $where .= " AND h.money_change_type IN (".implode(',', array_map('intval', $types)).") ";
        if (array_key_exists('start', $arrRqData) && strlen($arrRqData['start']) > 0) {
            $where .= " AND h.money_update_time >= ".$this->mDb->escape($arrRqData['start'])." ";
        }
        if (array_key_exists('end', $arrRqData) && strlen($arrRqData['end']) > 0) {
            $where .= " AND h.money_update_time <= ".$this->mDb->escape($arrRqData['end'].' 23:59:59')." ";
        }
        if (array_key_exists('mb_emp_fid', $arrRqData) && intval($arrRqData['mb_emp_fid']) > 0) {
            $where .= " AND m.mb_emp_fid = '".intval($arrRqData['mb_emp_fid'])."' ";
        }

        $strSql = " SELECT h.money_fid, h.money_mb_uid, m.mb_nickname, ";
        $strSql.= " h.money_before, h.money_after, h.money_amount, h.money_change_type, h.money_update_time ";
        $strSql.= " FROM ".$this->mTbName." h ";
        $strSql.= " INNER JOIN member m ON m.mb_uid = h.money_mb_uid ";
        $strSql.= " WHERE ".$where;
        $strSql.= " ORDER BY h.money_update_time DESC, h.money_fid DESC ";

        $rows = $this->mDb->query($strSql)->getResult();
        $out = [];
        foreach ($rows as $row) {
            $type = intval($row->money_change_type);
            $amt = abs(intval($row->money_amount));
            $obj = new \StdClass;
            $obj->money_fid = $row->money_fid;
            $obj->mb_uid = $row->money_mb_uid;
            $obj->mb_nickname = $row->mb_nickname;
            $obj->money_before = intval($row->money_before);
            $obj->money_after = intval($row->money_after);
            $obj->charge_amount = in_array($type, $chargeTypes, true) ? $amt : 0;
            $obj->exchange_amount = in_array($type, $exchangeTypes, true) ? $amt : 0;
            $obj->point_amount = in_array($type, $pointTypes, true) ? $amt : 0;
            $obj->money_change_type = $type;
            $obj->money_update_time = $row->money_update_time;
            $out[] = $obj;
        }
        return $out;
    }


    /**
     * 포인트전환(type=10) 건수. 총판: mb_emp_fid로 하부 매장만.
     */
    function searchPointConvertCount($arrRqData)
    {
        try {
            $where = "h.money_change_type = '".intval(POINTCHANGE_EXCHANGE)."' ";
            if (array_key_exists('start', $arrRqData) && strlen($arrRqData['start']) > 0) {
                $where .= " AND h.money_update_time >= ".$this->mDb->escape($arrRqData['start'])." ";
            }
            if (array_key_exists('end', $arrRqData) && strlen($arrRqData['end']) > 0) {
                $where .= " AND h.money_update_time <= ".$this->mDb->escape($arrRqData['end'].' 23:59:59')." ";
            }
            if (array_key_exists('mb_uid', $arrRqData) && strlen(trim($arrRqData['mb_uid'])) > 0) {
                $where .= " AND h.money_mb_uid LIKE ".$this->mDb->escape('%'.trim($arrRqData['mb_uid']).'%')." ";
            }
            if (array_key_exists('mb_emp_fid', $arrRqData) && intval($arrRqData['mb_emp_fid']) > 0) {
                $where .= " AND m.mb_emp_fid = '".intval($arrRqData['mb_emp_fid'])."' ";
            }
            $sql = "SELECT COUNT(*) AS cnt FROM ".$this->mTbName." h "
                ." INNER JOIN member m ON m.mb_fid = h.money_mb_fid "
                ." WHERE ".$where;
            $row = $this->mDb->query($sql)->getRow();
            return $row ? intval($row->cnt) : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    function searchPointConvertList($arrRqData, $page, $cntPer = 50)
    {
        try {
            if ($page < 1 || $cntPer < 1) {
                return [];
            }
            $where = "h.money_change_type = '".intval(POINTCHANGE_EXCHANGE)."' ";
            if (array_key_exists('start', $arrRqData) && strlen($arrRqData['start']) > 0) {
                $where .= " AND h.money_update_time >= ".$this->mDb->escape($arrRqData['start'])." ";
            }
            if (array_key_exists('end', $arrRqData) && strlen($arrRqData['end']) > 0) {
                $where .= " AND h.money_update_time <= ".$this->mDb->escape($arrRqData['end'].' 23:59:59')." ";
            }
            if (array_key_exists('mb_uid', $arrRqData) && strlen(trim($arrRqData['mb_uid'])) > 0) {
                $where .= " AND h.money_mb_uid LIKE ".$this->mDb->escape('%'.trim($arrRqData['mb_uid']).'%')." ";
            }
            if (array_key_exists('mb_emp_fid', $arrRqData) && intval($arrRqData['mb_emp_fid']) > 0) {
                $where .= " AND m.mb_emp_fid = '".intval($arrRqData['mb_emp_fid'])."' ";
            }
            $offset = $cntPer * ($page - 1);
            $sql = "SELECT h.money_fid, h.money_mb_uid, m.mb_nickname, "
                ." h.money_amount, h.money_before, h.money_after, h.money_update_time "
                ." FROM ".$this->mTbName." h "
                ." INNER JOIN member m ON m.mb_fid = h.money_mb_fid "
                ." WHERE ".$where
                ." ORDER BY h.money_fid DESC "
                ." LIMIT ".intval($cntPer)." OFFSET ".intval($offset);
            return $this->mDb->query($sql)->getResult();
        } catch (\Exception $e) {
            return [];
        }
    }


}