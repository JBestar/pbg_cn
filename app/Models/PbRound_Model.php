<?php 
namespace App\Models;

use CodeIgniter\Model;
use Config\Database;

class PbRound_Model extends Model {

    protected $table      = 'round_powerball';
    protected $primaryKey = 'round_fid';

    protected $returnType = 'object'; 
    private $gameId = GAME_BOGLE_BALL; 

    public function setType($gameId){
        switch($gameId){
            case GAME_POWER_BALL:   $this->table = 'round_powerball';   break;
            case GAME_BOGLE_BALL:   $this->table = 'round_bgb';   break;
            case GAME_COIN5_BALL:   $this->table = 'round_coin5';   break;
            default: break;
        }
        $this->gameId = $gameId;
    }

    public function gets($count){
        
        return $this->orderBy('round_fid', 'DESC')
                    ->findAll($count, 0); 
    }

    public function getByFid($round_fid){
        
        $this->find($round_fid);
    }

    
    public function searchCount($arrRqData){
        
        $where = "round_fid > 0 ";
        if(strlen($arrRqData['start']) > 0){
            $where .= " AND round_date >= '".$arrRqData['start']."' ";    
        }
        if(strlen($arrRqData['end']) > 0){
            $where .= " AND round_date <= '".$arrRqData['end']."' ";    
        }
        if(strlen($arrRqData['round_id']) > 0){
            $where .= " AND round_num = '".$arrRqData['round_id']."' ";    
        }

        $data = $this->where($where)
                ->findAll();

        return count($data);
            
    }


    
    public function searchList($arrRqData, $page, $cntPer = 20){
        
            
        if($page < 1)
            return NULL;
        if($cntPer < 1)
            return NULL;

        $where = " round_fid > 0 ";
        if(strlen($arrRqData['start']) > 0){
            $where .= " AND round_date >= '".$arrRqData['start']."' ";    
        }
        if(strlen($arrRqData['end']) > 0){
            $where .= " AND round_date <= '".$arrRqData['end']."' ";    
        }
        if(strlen($arrRqData['round_id']) > 0){
            $where .= " AND round_num = '".$arrRqData['round_id']."' ";    
        }
        
        $joinTbName = "bet_powerball";
        $strSql = "SELECT * FROM ".$this->table;
        $strSql.= " LEFT JOIN ( SELECT bet_round_fid, COUNT(bet_fid) AS bet_count, SUM(bet_money) AS bet_sum, ";
            $strSql.= " SUM(bet_win_money) AS win_sum, SUM(bet_empl_amount) AS empl_sum, SUM(bet_agen_amount) AS agen_sum"; 
            $strSql.= " FROM ".$joinTbName;
            if(array_key_exists('mb_emp_fid', $arrRqData)){
                $strSql.=" WHERE bet_emp_fid = '".$arrRqData['mb_emp_fid']."' AND bet_game = '".$this->gameId."' ";
            }
            $strSql.= " GROUP BY bet_round_fid ) AS bet_acc ON bet_acc.bet_round_fid = ".$this->table.".round_fid ";
        $strSql.= " WHERE ".$where." ORDER BY round_fid DESC ";
        $nStartRow = ($page-1) * $cntPer ;
        $strSql.=" LIMIT ".$nStartRow.", ".$cntPer;

        return $this->db->query($strSql)->getResult();

    }

}