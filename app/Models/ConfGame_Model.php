<?php 
namespace App\Models;

use CodeIgniter\Model;
use Config\Database;

class ConfGame_Model extends Model {

    private $mDb;
    private $mBuilder;
    private $mTbName = "conf_game";
    private $mTbColumn;

    function __construct()
    {
      
        $this->mDb      = Database::connect();
        $this->mBuilder = $this->mDb->table($this->mTbName);
    }

    public function getById($game_id){
        
        try { 
            
            $this->mBuilder->where('game_id', $game_id)
                           ->getCompiledSelect(false);
            $query = $this->mBuilder->get();
            return $query->getRow();
            
        } catch (\Exception $e) {  
            return NULL;
        }
        return NULL;
    }

    public function getByEmpId($emp_id){
        
        try { 
            
            $this->mBuilder->where('game_emp_fid', $emp_id)
                           ->getCompiledSelect(false);
            $query = $this->mBuilder->get();
            $objConf = $query->getRow();
            
            if(is_null($objConf)){
                $this->mBuilder ->where('game_emp_fid', 0)
                                ->getCompiledSelect(false);
                $query = $this->mBuilder->get();
                $objConf = $query->getRow();                    
            }

            return $objConf;
        } catch (\Exception $e) {  
            return NULL;
        }
        return NULL;
    }

    
    public function updateById($game_id, $arrRqData){

        $bUpdate = false;
        if(array_key_exists('game_time_countdown', $arrRqData)){
            $this->mBuilder->set('game_time_countdown', $arrRqData['game_time_countdown']);
            $bUpdate = true;
        }
            
        if(!$bUpdate)
            return false;
        
        $this->mBuilder->where('game_id', $game_id);

        return $this->mBuilder->update();   //if success, return true
       
    }

}