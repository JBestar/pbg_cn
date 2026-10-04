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

    /** Replying to a store counts as handling every unread memo that store sent to $me. */
    public function markReadFromSender($me, $senderUid)
    {
        if ($me === '' || $senderUid === '') {
            return false;
        }
        return $this->mDb->table($this->mTbName)
            ->where('notice_type', NOTICE_TYPE_MSG)
            ->where('notice_recv_uid', $me)
            ->where('notice_send_uid', $senderUid)
            ->where('notice_recv_read', 0)
            ->where('notice_recv_delete', 0)
            ->set('notice_recv_read', 1)
            ->update();
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
    
    public function waitProc($recv_uid)
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

    /** Unread inbox memos (쪽지), optionally filter by title. */
    public function waitMemoUnread($recv_uid, $title = '')
    {
        try {
            $where = " notice_recv_delete = '0' AND ";
            $where .= " notice_recv_read = '0' AND ";
            $where .= " notice_type = '".NOTICE_TYPE_MSG."' AND";
            $where .= " notice_recv_uid = '".$recv_uid."' ";
            if ($title !== '') {
                $where .= " AND notice_title = '".$this->mDb->escapeString($title)."' ";
            }
            $this->mBuilder->where($where)->getCompiledSelect(false);
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




    /**
     * Agency/HQ mailbox: sent (not send-deleted) OR received (not recv-deleted).
     * Optional peer_uid / recv_uid filters by the other party (store id).
     */
    private function mailboxBuilder($me, $arrRqData, $notice_type)
    {
        $builder = $this->mDb->table($this->mTbName);
        $builder->where('notice_type', (int)$notice_type);

        $pending = is_array($arrRqData) && !empty($arrRqData['pending']);
        if ($pending) {
            $builder->where('notice_recv_uid', $me);
            $builder->where('notice_recv_delete', 0);
            $builder->where('notice_recv_read', 0);
        } else {
            $builder->groupStart()
                ->groupStart()
                    ->where('notice_send_uid', $me)
                    ->where('notice_send_delete', 0)
                ->groupEnd()
                ->orGroupStart()
                    ->where('notice_recv_uid', $me)
                    ->where('notice_recv_delete', 0)
                ->groupEnd()
            ->groupEnd();
        }

        $peer = '';
        if (is_array($arrRqData)) {
            if (!empty($arrRqData['peer_uid'])) {
                $peer = trim((string)$arrRqData['peer_uid']);
            } elseif (!empty($arrRqData['recv_uid'])) {
                $peer = trim((string)$arrRqData['recv_uid']);
            }
        }
        if ($peer !== '') {
            $builder->groupStart()
                ->where('notice_send_uid', $peer)
                ->orWhere('notice_recv_uid', $peer)
            ->groupEnd();
        }
        if (is_array($arrRqData) && !empty($arrRqData['start'])) {
            $builder->where('notice_create_time >=', $arrRqData['start']);
        }
        if (is_array($arrRqData) && !empty($arrRqData['end'])) {
            $builder->where('notice_create_time <=', $arrRqData['end'] . ' 23:59:59');
        }
        return $builder;
    }

    public function mailboxCount($me, $arrRqData, $notice_type)
    {
        try {
            return $this->mailboxBuilder($me, $arrRqData, $notice_type)->countAllResults();
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function mailboxList($me, $arrRqData, $notice_type, $page, $cntPer = 20)
    {
        try {
            if ($page < 1 || $cntPer < 1) {
                return null;
            }
            $query = $this->mailboxBuilder($me, $arrRqData, $notice_type)
                ->orderBy('notice_fid', 'DESC')
                ->get($cntPer, $cntPer * ($page - 1));
            return $query ? $query->getResult() : null;
        } catch (\Exception $e) {
            return null;
        }
    }


    /**
     * Agency announcements: one row per notice (recv_uid empty),
     * visible to every store whose parent agency is notice_send_uid.
     */
    private function announceBuilder($senderUid, $arrRqData)
    {
        $builder = $this->mDb->table($this->mTbName);
        $builder->where('notice_type', NOTICE_TYPE_NOTICE)
            ->where('notice_send_uid', $senderUid)
            ->where('notice_send_delete', 0);

        if (is_array($arrRqData) && !empty($arrRqData['title'])) {
            $builder->like('notice_title', trim((string)$arrRqData['title']));
        }
        if (is_array($arrRqData) && !empty($arrRqData['start'])) {
            $builder->where('notice_create_time >=', $arrRqData['start']);
        }
        if (is_array($arrRqData) && !empty($arrRqData['end'])) {
            $builder->where('notice_create_time <=', $arrRqData['end'] . ' 23:59:59');
        }
        return $builder;
    }

    public function announceCount($senderUid, $arrRqData)
    {
        try {
            return $this->announceBuilder($senderUid, $arrRqData)->countAllResults();
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function announceList($senderUid, $arrRqData, $page, $cntPer = 20)
    {
        try {
            if ($page < 1 || $cntPer < 1) {
                return null;
            }
            $query = $this->announceBuilder($senderUid, $arrRqData)
                ->orderBy('notice_fid', 'DESC')
                ->get($cntPer, $cntPer * ($page - 1));
            return $query ? $query->getResult() : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function registerAnnounce($senderUid, $title, $content)
    {
        if ($senderUid === '' || $title === '' || $content === '') {
            return false;
        }
        return $this->mDb->table($this->mTbName)
            ->set('notice_create_time', 'NOW()', false)
            ->insert([
            'notice_type'        => NOTICE_TYPE_NOTICE,
            'notice_title'       => $title,
            'notice_content'     => $content,
            'notice_send_uid'    => $senderUid,
            'notice_recv_uid'    => '',
            'notice_send_read'   => 0,
            'notice_recv_read'   => 0,
            'notice_send_delete' => 0,
            'notice_recv_delete' => 0,
            'notice_answer_state'=> 0,
        ]);
    }

    public function modifyAnnounce($senderUid, $noticeId, $title, $content)
    {
        if ($senderUid === '' || $noticeId < 1 || $title === '' || $content === '') {
            return false;
        }
        $builder = $this->mDb->table($this->mTbName);
        $builder->where('notice_fid', $noticeId)
            ->where('notice_type', NOTICE_TYPE_NOTICE)
            ->where('notice_send_uid', $senderUid)
            ->where('notice_send_delete', 0)
            ->set('notice_title', $title)
            ->set('notice_content', $content)
            ->update();
        return $this->mDb->affectedRows() > 0 || $this->announceOwned($senderUid, $noticeId);
    }

    public function deleteAnnounce($senderUid, $noticeId)
    {
        if ($senderUid === '' || $noticeId < 1) {
            return false;
        }
        $this->mDb->table($this->mTbName)
            ->where('notice_fid', $noticeId)
            ->where('notice_type', NOTICE_TYPE_NOTICE)
            ->where('notice_send_uid', $senderUid)
            ->where('notice_send_delete', 0)
            ->set('notice_send_delete', 1)
            ->update();
        return $this->mDb->affectedRows() > 0;
    }

    /** Same-content updates report 0 affected rows; treat them as success when the row is still owned. */
    private function announceOwned($senderUid, $noticeId)
    {
        return $this->mDb->table($this->mTbName)
            ->where('notice_fid', $noticeId)
            ->where('notice_type', NOTICE_TYPE_NOTICE)
            ->where('notice_send_uid', $senderUid)
            ->where('notice_send_delete', 0)
            ->countAllResults() > 0;
    }


}