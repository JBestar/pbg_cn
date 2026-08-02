<?php 
namespace App\Models;

use CodeIgniter\Model;
use Config\Database;

class Notice_Model extends Model {

    private $mDb;
    private $mBuilder;
    private $mTbName = "board_notice";
    private $mTbColumn;

    function __construct()
    {
      
        $this->mDb      = Database::connect();
        $this->mBuilder = $this->mDb->table($this->mTbName);
    }


    public function getById($notice_id, $notice_type = -1){
        
        try { 
            $where = "notice_fid = '".$notice_id."' ";
            if($notice_type >= 0){
                $where .= " AND notice_type = '".$notice_type."' ";    
            }

            $this->mBuilder->where($where)
                           ->getCompiledSelect(false);
            $query = $this->mBuilder->get();
            return $query->getRow();
            
        } catch (\Exception $e) {  
            return NULL;
        }
        return NULL;
    }

    public function readById($notice_id, $bSend){

        if($bSend)
            $this->mBuilder->set('notice_send_read', '1');
        else         
            $this->mBuilder->set('notice_recv_read', '1');
        
            $this->mBuilder->where('notice_fid', $notice_id);
        return $this->mBuilder->update();   //if success, return true
    }

    
    public function deleteSendById($notice_id){

        $this->mBuilder->set('notice_send_delete', '1');        
        $this->mBuilder->where('notice_fid', $notice_id);
        return $this->mBuilder->update();   //if success, return true
    }
    
    public function deleteRecvById($notice_id){

        $this->mBuilder->set('notice_recv_delete', '1');        
        $this->mBuilder->where('notice_fid', $notice_id);
        return $this->mBuilder->update();   //if success, return true
    }
    
    public function registerQna($arrRqData, $objMember){
        if(strlen($arrRqData['title']) < 1 || strlen($arrRqData['content']) < 1)
            return false;

        $this->mBuilder->set('notice_type', NOTICE_TYPE_QNA);    
        $this->mBuilder->set('notice_title', $arrRqData['title']);   
        $this->mBuilder->set('notice_content', $arrRqData['content']); 
        $this->mBuilder->set('notice_send_uid', $objMember->mb_uid);    
        $this->mBuilder->set('notice_recv_uid', $objMember->mb_emp_uid);
        $this->mBuilder->set('notice_create_time', 'NOW()', false);

        return $this->mBuilder->insert();   //if success, return true
    }

    public  function answerQna($arrRqData)
    {
        $this->mBuilder->set('notice_answer', $arrRqData['answer']);      
        $this->mBuilder->set('notice_answer_state', '1'); 
        $this->mBuilder->set('notice_send_read', '0'); 
        $this->mBuilder->set('notice_send_delete', '0');      
        $this->mBuilder->set('notice_recv_read', '1');      

        $this->mBuilder->where('notice_fid', $arrRqData['no']);
        return $this->mBuilder->update();   //if success, return true
    }

    public function registerMemo($arrRqData){
        if(strlen($arrRqData['title']) < 1 || strlen($arrRqData['content']) < 1)
            return false;

        $this->mBuilder->set('notice_type', NOTICE_TYPE_MSG);    
        $this->mBuilder->set('notice_title', $arrRqData['title']);   
        $this->mBuilder->set('notice_content', $arrRqData['content']); 
        $this->mBuilder->set('notice_send_uid', $arrRqData['send_uid']);    
        $this->mBuilder->set('notice_recv_uid', $arrRqData['recv_uid']);
        $this->mBuilder->set('notice_create_time', 'NOW()', false);

        return $this->mBuilder->insert();   //if success, return true
    }

    
    public  function modifyMemo($arrRqData)
    {
        $this->mBuilder->set('notice_title', $arrRqData['title']);   
        $this->mBuilder->set('notice_content', $arrRqData['content']);
        $this->mBuilder->set('notice_recv_read', '0');      
        $this->mBuilder->set('notice_recv_delete', '0'); 
        $this->mBuilder->set('notice_create_time', 'NOW()', false);

        $this->mBuilder->where('notice_fid', $arrRqData['no']);
        $this->mBuilder->where('notice_send_uid', $arrRqData['send_uid']);

        return $this->mBuilder->update();   //if success, return true
    }
    
    public  function waitProc($recv_uid)
    {
        
    	try { 
            $where = " notice_answer_state = '0' AND ";
            $where.= " notice_recv_delete = '0' AND ";
            $where.= " notice_type = '".NOTICE_TYPE_QNA."' AND";            
            $where.= " notice_recv_uid = '".$recv_uid."' ";
            
            $this->mBuilder ->where($where)
                            ->getCompiledSelect(false);
            
            return $this->mBuilder->countAllResults();
            
        } catch (\Exception $e) {  
            return 0;
        }
        return 0;
    }

    public function searchCount($arrRqData, $notice_type){
        
        try { 
            
            $where = "notice_type = '".$notice_type."' ";
            if(array_key_exists('send_uid', $arrRqData) && strlen($arrRqData['send_uid']) > 0 ){
                $where .= " AND notice_send_uid = '".$arrRqData['send_uid']."' ";   
                $where .= " AND notice_send_delete = '0' ";
            }
            if(array_key_exists('recv_uid', $arrRqData) && strlen($arrRqData['recv_uid']) > 0 ){
                $where .= " AND notice_recv_uid = '".$arrRqData['recv_uid']."' ";    
                
                if(!array_key_exists('send_uid', $arrRqData))  
                    $where .= " AND notice_recv_delete = '0' ";
            } 
           
            if( array_key_exists('start', $arrRqData) && strlen($arrRqData['start']) > 0 ){
                $where .= " AND notice_create_time >= '".$arrRqData['start']."' ";    
            }
            if(array_key_exists('end', $arrRqData) && strlen($arrRqData['end']) > 0){
                $where .= " AND notice_create_time <= '".$arrRqData['end']." 23:59:59' ";    
            }  

            $this->mBuilder ->where($where)
                            ->getCompiledSelect(false);

            return $this->mBuilder->countAllResults();
            
        } catch (\Exception $e) {  
            return 0;
        }
        return 0;
    }


    
    public function searchList($arrRqData, $notice_type, $page, $cntPer = 20){
        
        try { 
            
            if($page < 1)
                return NULL;
            if($cntPer < 1)
                return NULL;

            $where = "notice_type = '".$notice_type."' ";
            if(array_key_exists('send_uid', $arrRqData) && strlen($arrRqData['send_uid']) > 0 ){
                $where .= " AND notice_send_uid = '".$arrRqData['send_uid']."' ";   
                $where .= " AND notice_send_delete = '0' ";
            }
            if(array_key_exists('recv_uid', $arrRqData) && strlen($arrRqData['recv_uid']) > 0 ){
                $where .= " AND notice_recv_uid = '".$arrRqData['recv_uid']."' ";  
                if(!array_key_exists('send_uid', $arrRqData))  
                    $where .= " AND notice_recv_delete = '0' ";
            } 
            
            if( array_key_exists('start', $arrRqData) && strlen($arrRqData['start']) > 0 ){
                $where .= " AND notice_create_time >= '".$arrRqData['start']."' ";    
            }
            if(array_key_exists('end', $arrRqData) && strlen($arrRqData['end']) > 0){
                $where .= " AND notice_create_time <= '".$arrRqData['end']." 23:59:59' ";    
            }  
            
            $this->mBuilder ->where($where)
                            ->orderBy('notice_fid', 'DESC')
                            ->getCompiledSelect(false);

            $query = $this->mBuilder->get($cntPer, $cntPer*($page-1));
            return $query->getResult();
            
        } catch (\Exception $e) {  
            return NULL;
        }
        return NULL;
    }




}