<?php 
namespace App\Models;

use CodeIgniter\Model;
use Config\Database;

class Captcha_Model extends Model {

    private $mDb;
    private $mBuilder;
    private $mTbName = "log_captcha";
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

    public function addLog($file, $sn){


    	$this->mBuilder->set('log_file', $file);
        $this->mBuilder->set('log_sn', $sn);
        $this->mBuilder->set('log_type', CAPTCHA_ADMIN);
        $this->mBuilder->set('log_time', 'NOW()', false);
        
        return $this->mBuilder->insert();
        
    }

    public function getByFile($file){
        
        try { 
            
            $where = "log_file = '".$file."' ";
            $where.= " AND log_type = '".CAPTCHA_ADMIN."' ";
            
            
            $this->mBuilder ->where($where)            
                            ->getCompiledSelect(false);

            $query = $this->mBuilder->get();
            return $query->getResult();
            
        } catch (\Exception $e) {  
            return NULL;
        }
        return NULL;
    }

    public function verify($file, $captcha){
        try { 
            
            $arrCaptcha = $this->getByFile($file);
            
            if(is_null($arrCaptcha) || count($arrCaptcha) == 0){
                return RESULT_CAPTCHA_NONE;
            } 
            foreach($arrCaptcha as $objCaptcha){
                if($objCaptcha->log_sn === $captcha){
                    return RESULT_OK;
                }
            }
            return RESULT_CAPTCHA_ERR;
            
        } catch (\Exception $e) {  
            return RESULT_CAPTCHA_NONE;
        }
        return RESULT_CAPTCHA_NONE;
    }

}