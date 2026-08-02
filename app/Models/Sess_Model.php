<?php 
namespace App\Models;

use CodeIgniter\Model;
use Config\Database;

class Sess_Model extends Model {

    private $mDb;
    private $mBuilder;
    private $mTbName = "sess_list";
    private $mTbColumn;

    function __construct()
    {      
        $this->mDb      = Database::connect();
        $this->mBuilder = $this->mDb->table($this->mTbName);
    }

    
    public function getById($sess_id){
        
        try {
            
            $this->mBuilder ->where('sess_id', $sess_id)
                            ->getCompiledSelect(false);
            $query = $this->mBuilder->get();
            return $query->getRow();
            
        } catch (\Exception $e) {  
            return NULL;
        }
        return NULL;
    }

    public function getByUid($mb_uid){
        
        try {
            
            $this->mBuilder ->where('sess_mb_uid', $mb_uid)
                            ->getCompiledSelect(false);
            $query = $this->mBuilder->get();
            return $query->getResult();
            
        } catch (\Exception $e) {  
            return NULL;
        }
        return NULL;
    }


    
    public function login($objMember, $sess_id){

        $this->mBuilder->set('sess_id', $sess_id);
        $this->mBuilder->set('sess_mb_fid', $objMember->mb_fid);
        $this->mBuilder->set('sess_mb_uid', $objMember->mb_uid);
        $this->mBuilder->set('sess_mb_level', $objMember->mb_level);
        $this->mBuilder->set('sess_emp_fid', $objMember->mb_emp_fid);
        $this->mBuilder->set('sess_ip', $objMember->mb_ip_last);
        $this->mBuilder->set('sess_update_time', 'NOW()', false);
        
        return $this->mBuilder->insert();   //if success, return true
    }
    

    public function updateLast($sess_id){

        $this->mBuilder->set('sess_update_time', 'NOW()', false);
        
        $this->mBuilder->where('sess_id', $sess_id);

        return $this->mBuilder->update();   //if success, return true
    }

    
    public function deleteById($sess_id){
        
        $this->mBuilder->where('sess_id', $sess_id);
        return $this->mBuilder->delete();   //if success, return true
    }
    
    public function deleteByUid($mb_uid){
        
        $this->mBuilder->where('sess_mb_uid', $mb_uid);
        return $this->mBuilder->delete();   //if success, return true
    }

    public function deleteLast(){
        
        $tmLimit = date("Y-m-d H:i:s", strtotime("-2 minutes", time()));

        $this->mBuilder->where('sess_update_time < ', $tmLimit);
        return $this->mBuilder->delete();   //if success, return true
    }


}