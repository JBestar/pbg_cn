<?php 
namespace App\Models;

use CodeIgniter\Model;
use Config\Database;

class ConfSite_Model extends Model {

    private $mDb;
    private $mBuilder;
    private $mTbName = "conf_site";
    private $mTbColumn;

    function __construct()
    {      
        $this->mDb      = Database::connect();
        $this->mBuilder = $this->mDb->table($this->mTbName);
    }

    public function getConf($conf_id){
        
        try { 
            
            $this->mBuilder->where('conf_id', $conf_id)
                           ->getCompiledSelect(false);
            $query = $this->mBuilder->get();
            return $query->getRow();            
        } catch (\Exception $e) {  
            return NULL;
        }
        return NULL;
    }

    
    public function updateById($conf_id, $arrRqData){

        $bUpdate = false;
        if(array_key_exists('conf_content', $arrRqData)){
            $this->mBuilder->set('conf_content', $$arrRqData['conf_content']);
            $bUpdate = true;
        }
            
        if(array_key_exists('conf_active', $arrRqData)){
            $this->mBuilder->set('conf_active', $arrRqData['conf_active']);
            $bUpdate = true;
        }
        if(!$bUpdate)
            return false;
        
        $this->mBuilder->where('conf_id', $conf_id);

        return $this->mBuilder->update();   //if success, return true
       
    }

    public function getSiteName(){

        $objConf = $this->getConf(CONF_SITENAME);
        $strSiteName = "";
        if(!is_null($objConf)){
            $strSiteName = $objConf->conf_content;
        }
        return $strSiteName;
    }

    public function IsMaintain(){

        $objMaintain = $this->getConf(CONF_MAINTAIN);
        if(!is_null($objMaintain) && $objMaintain->conf_active == 1) {
            return true;
        }
        return false;
       
    }


}