<?php

  	function is_login(){ 
      if(!isset($_SESSION['logged_in']))
        return false;
      else if($_SESSION['logged_in']==TRUE)
        return true;
      else return false;  
  	}

    //사이드바의 선택상태 초기화 배렬을 반환해주는 함수
    function getSidebarArray(){

      $locale = isset($_SESSION['locale']) ? $_SESSION['locale'] : 'ko';
      if (!in_array($locale, ['ko', 'zh', 'en'], true)) {
        $locale = 'ko';
      }

      return array(
              'menuitem_1' => '',
              'menuitem_2' => '',
              'menuitem_3' => '',
              'menuitem_4' => '',
              'menuitem_5' => '',
              'menuitem_6' => '',
              'menuitem_7' => '',
              'menuitem_8' => '',
              'menuitem_9' => '',
              'menuitem_10' => '',
              'menuitem_11' => '',
              'admin_locale' => $locale,
          );

    }

    function getCaptchaFiles($dir, &$arrInfo)
    {
      if (substr($dir, strlen($dir)-1, 1) != DIRECTORY_SEPARATOR)
          $dir .= DIRECTORY_SEPARATOR;

      if(!file_exists($dir)){
        return;
      }

      if ($handle = opendir($dir))
      {
          while ($obj = readdir($handle))
          {
              if ($obj != '.' && $obj != '..')
              {
                  if (is_file($dir.$obj) && strlen($obj) > 4)
                  {
                    if( strtoLower(substr($obj, strlen($obj)-4, 4)) === ".jpg")
                      array_push($arrInfo, substr($obj, 0, strlen($obj)-4));
                  }                  
              }
          }

          closedir($handle);
      }
    
    }


    
    function getPbRoundInfo(){

      $tmNow = time()+TM_OFFSET - 10;
      $nYear = date("Y",$tmNow);
      $nMonth = date("m",$tmNow);
      $nDay = date("d",$tmNow);

      $nHour = date("G",$tmNow);
      $nMin = date("i",$tmNow);
      //$second = date("s",$tmNow);

      $nSumMinutes = $nHour * 60 + $nMin;
      $nRoundNo = floor($nSumMinutes / 5) ;
      $nRoundNo = $nRoundNo % 288 + 1;
      $arrRoundInfo['round_no'] = $nRoundNo;

      $strDate = "";
      if($nSumMinutes < 1440){
        $strDate = date( 'Y-m-d', $tmNow );
      }
      else {
        $strDate = date('Y-m-d', strtotime("+1 day", $tmNow));
      }

      $arrRoundInfo['round_date'] = $strDate;

      $nSumMinutes = $nRoundNo * 5;
      $nHour = $nSumMinutes / 60;
      $nHour = floor($nHour);
      $nMinute = $nSumMinutes % 60;

      //현재시간설정      
      $tmRoundCurrent = date("Y-m-d H:i:s", $tmNow);        
      $arrRoundInfo['round_current'] = $tmRoundCurrent;

      //회차 마감시간설정
      $strRoundEnd = $strDate." ".$nHour.":".$nMinute.":"."0";
      $tmRoundEnd = strtotime($strRoundEnd);
      $arrRoundInfo['round_end'] = date("Y-m-d H:i:s", $tmRoundEnd);
      
      //회차 시작시간설정
      $tmRoundStart = strtotime("-5 minutes", $tmRoundEnd);
      $arrRoundInfo['round_start'] = date("Y-m-d H:i:s", $tmRoundStart);
      
      return $arrRoundInfo;
    }

    
    function getPbLastRoundInfo(){

      $tmNow = time()+TM_OFFSET;
      $nYear = date("Y",$tmNow);
      $nMonth = date("m",$tmNow);
      $nDay = date("d",$tmNow);

      $nHour = date("G",$tmNow);
      $nMin = date("i",$tmNow);

      $nSumMinutes = $nHour * 60 + $nMin;
      $nRoundNo = floor($nSumMinutes / 5) ;
      $nRoundNo = $nRoundNo % 288 + 1;

      $nRoundNo -= 1;
      $strDate = "";
      if($nRoundNo < 1){
        $nRoundNo = 288;
        $strDate = date('Y-m-d', strtotime("-1 day", $tmNow));
      }
      else {
        $strDate = date( 'Y-m-d', $tmNow );
      }

      $arrRoundInfo['round_no'] = $nRoundNo;
      $arrRoundInfo['round_date'] = $strDate;


      return $arrRoundInfo;
    }

    function setBetRatio(&$arrBetData, $objConf){
      if(is_null($objConf))
        return false;
      $strRatio = "-1";
      $nMode = (int)($arrBetData['mode']);
      $bMix = false;
      switch ($nMode) {
        case 0: $strRatio = $objConf->game_ratio_1; $arrBetData['target'] = 'P'; break;
        case 1: $strRatio = $objConf->game_ratio_1; $arrBetData['target'] = 'B'; break;
        case 2: $strRatio = $objConf->game_ratio_2; $arrBetData['target'] = 'P'; break;
        case 3: $strRatio = $objConf->game_ratio_2; $arrBetData['target'] = 'B'; break;
        case 4: $strRatio = $objConf->game_ratio_9; $arrBetData['target'] = 'PP'; $bMix = true; break;
        case 5: $strRatio = $objConf->game_ratio_10; $arrBetData['target'] = 'PB'; $bMix = true; break;
        case 6: $strRatio = $objConf->game_ratio_11; $arrBetData['target'] = 'BP'; $bMix = true; break;
        case 7: $strRatio = $objConf->game_ratio_12; $arrBetData['target'] = 'BB'; $bMix = true; break;
        case 8: $strRatio = $objConf->game_ratio_13; $arrBetData['target'] = 'L'; break;
        case 9: $strRatio = $objConf->game_ratio_14; $arrBetData['target'] = 'M'; break;
        case 10: $strRatio = $objConf->game_ratio_15; $arrBetData['target'] = 'S'; break;
        case 11: $strRatio = $objConf->game_ratio_16; $arrBetData['target'] = 'PL'; $bMix = true; break;
        case 12: $strRatio = $objConf->game_ratio_17; $arrBetData['target'] = 'PM'; $bMix = true; break;
        case 13: $strRatio = $objConf->game_ratio_18; $arrBetData['target'] = 'PS'; $bMix = true; break;
        case 14: $strRatio = $objConf->game_ratio_19; $arrBetData['target'] = 'BL'; $bMix = true; break;
        case 15: $strRatio = $objConf->game_ratio_20; $arrBetData['target'] = 'BM'; $bMix = true; break;
        case 16: $strRatio = $objConf->game_ratio_21; $arrBetData['target'] = 'BS'; $bMix = true; break;

        case 17: $strRatio = $objConf->game_ratio_3; $arrBetData['target'] = 'P'; break;
        case 18: $strRatio = $objConf->game_ratio_3; $arrBetData['target'] = 'B'; break;
        case 19: $strRatio = $objConf->game_ratio_4; $arrBetData['target'] = 'P'; break;
        case 20: $strRatio = $objConf->game_ratio_4; $arrBetData['target'] = 'B'; break;
        case 21: $strRatio = $objConf->game_ratio_5; $arrBetData['target'] = 'PP'; $bMix = true; break;
        case 22: $strRatio = $objConf->game_ratio_6; $arrBetData['target'] = 'PB'; $bMix = true; break;
        case 23: $strRatio = $objConf->game_ratio_7; $arrBetData['target'] = 'BP'; $bMix = true; break;
        case 24: $strRatio = $objConf->game_ratio_8; $arrBetData['target'] = 'BB'; $bMix = true; break;
        
        case 33:
        case 34:
        case 35:
        case 36:
        case 37:
        case 38:
        case 39:  
        case 40: $strRatio = $objConf->game_ratio_22; $arrBetData['target'] = 'PPP'; $bMix = true; break;
        case 41:
        case 42:
        case 43:
        case 44:
        case 45:
        case 46:
        case 47:
        case 48:
        case 49:
        case 50: $strRatio = $objConf->game_ratio_23; $arrBetData['target'] = 'Q'; break;
          
        default: break;
      }

      if(floatval($strRatio) < 0)   
        return false;
      $arrBetData['ratio'] = floatval($strRatio);
      return true;
    }

    function setChgRatio(&$arrBetData, $objConf){
      if(is_null($objConf))
        return false;
      $strRatio = "-1";
      $nMode = (int)($arrBetData['mode']);
      $bMix = false;
      switch ($nMode) {
        case 0: $strRatio = $objConf->game_ratio_1; $arrBetData['mode'] = 1; $arrBetData['target'] = 'B'; break;
        case 1: $strRatio = $objConf->game_ratio_1; $arrBetData['mode'] = 0; $arrBetData['target'] = 'P'; break;
        case 2: $strRatio = $objConf->game_ratio_2; $arrBetData['mode'] = 3; $arrBetData['target'] = 'B'; break;
        case 3: $strRatio = $objConf->game_ratio_2; $arrBetData['mode'] = 2; $arrBetData['target'] = 'P'; break;
        
        case 17: $strRatio = $objConf->game_ratio_3; $arrBetData['mode'] = 18; $arrBetData['target'] = 'B'; break;
        case 18: $strRatio = $objConf->game_ratio_3; $arrBetData['mode'] = 17; $arrBetData['target'] = 'P'; break;
        case 19: $strRatio = $objConf->game_ratio_4; $arrBetData['mode'] = 20; $arrBetData['target'] = 'B'; break;
        case 20: $strRatio = $objConf->game_ratio_4; $arrBetData['mode'] = 19; $arrBetData['target'] = 'P'; break;
          
        default: break;
      }

      if(floatval($strRatio) < 0)   
        return false;
      $arrBetData['ratio'] = floatval($strRatio);
      return true;
    }
    
function writeLog($contenet){ 
    
  $tmNow = time() ;
  $nHour = date("G",$tmNow);
  $nMin = date("i",$tmNow);
  $nSec = date("s",$tmNow);

  $sDate = date( 'Y-m-d', $tmNow);
  $fLog = fopen(LOG_FILE.$sDate, "a") ;

  $tContent = "[".$nHour.":".$nMin.":".$nSec."] ".$contenet."\r\n";

  fputs($fLog, $tContent);
  fclose($fLog);
}

?>
