<?php 
namespace App\Models;

use CodeIgniter\Model;
use Config\Database;

class LogHist_Model extends Model {

    private $mDb;
    private $mBuilder;
    private $mTbName = "log_history";

    function __construct()
    {      
        $this->mDb      = Database::connect();
        $this->mBuilder = $this->mDb->table($this->mTbName);
    }

    public function addLog($objMember){

        $this->mBuilder->set('log_mb_uid', $objMember->mb_uid);
        $this->mBuilder->set('log_emp_fid', $objMember->mb_emp_fid);        
        $this->mBuilder->set('log_type', 0);
        $this->mBuilder->set('log_ip', $objMember->mb_ip_last);
        $this->mBuilder->set('log_time', 'NOW()', false);

        return $this->mBuilder->insert();   //if success, return true
    }

    public function addTest($log){

        $this->mBuilder->set('log_mb_uid', time());
        $this->mBuilder->set('log_ip', $log);

        $this->mBuilder->set('log_time', 'NOW()', false);

        return $this->mBuilder->insert();   //if success, return true

    }


}