<?php

//--------------------------------------------------------------------
// App Namespace
//--------------------------------------------------------------------
// This defines the default Namespace that is used throughout
// CodeIgniter to refer to the Application directory. Change
// this constant to change the namespace that all application
// classes should use.
//
// NOTE: changing this will require manually modifying the
// existing namespaces of App\* namespaced-classes.
//
defined('APP_NAMESPACE') || define('APP_NAMESPACE', 'Tiger');

/*
|--------------------------------------------------------------------------
| Composer Path
|--------------------------------------------------------------------------
|
| The path that Composer's autoload file is expected to live. By default,
| the vendor folder is in the Root directory, but you can customize that here.
*/
defined('COMPOSER_PATH') || define('COMPOSER_PATH', ROOTPATH . 'vendor/autoload.php');

/*
|--------------------------------------------------------------------------
| Timing Constants
|--------------------------------------------------------------------------
|
| Provide simple ways to work with the myriad of PHP functions that
| require information to be in seconds.
*/
defined('SECOND') || define('SECOND', 1);
defined('MINUTE') || define('MINUTE', 60);
defined('HOUR')   || define('HOUR', 3600);
defined('DAY')    || define('DAY', 86400);
defined('WEEK')   || define('WEEK', 604800);
defined('MONTH')  || define('MONTH', 2592000);
defined('YEAR')   || define('YEAR', 31536000);
defined('DECADE') || define('DECADE', 315360000);

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
defined('EXIT_SUCCESS')        || define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR')          || define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG')         || define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE')   || define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS')  || define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') || define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     || define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE')       || define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN')      || define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      || define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code


$base_url = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https://'.$_SERVER['HTTP_HOST'] : 'http://'.$_SERVER['HTTP_HOST']."".str_replace(basename($_SERVER['SCRIPT_NAME']), "", $_SERVER['SCRIPT_NAME']);
defined('BASEURL') || define('BASEURL', $base_url);

defined('DOWNLOADDIR')         || define('DOWNLOADDIR', "download");
defined('DOWNLOADROOT')        || define('DOWNLOADROOT', ROOTPATH."public".DIRECTORY_SEPARATOR.DOWNLOADDIR.DIRECTORY_SEPARATOR);

defined('ENV_PRODUCTION')       || define('ENV_PRODUCTION', 'production');
defined('ENV_DEVELOPMENT')      || define('ENV_DEVELOPMENT', 'development');

defined('LOG_FILE')             || define('LOG_FILE', ROOTPATH."logs".DIRECTORY_SEPARATOR);

defined('LEVEL_ADMIN')         || define('LEVEL_ADMIN', 9);
defined('LEVEL_COMPANY')       || define('LEVEL_COMPANY', 9);
defined('LEVEL_AGENCY')        || define('LEVEL_AGENCY', 8);
defined('LEVEL_EMPLOYEE')      || define('LEVEL_EMPLOYEE', 7);
defined('LEVEL_USER')          || define('LEVEL_USER', 1);

//permit state
defined('PERMIT_OK')           || define('PERMIT_OK', 1);
defined('PERMIT_CANCEL')       || define('PERMIT_CANCEL', 0);

//Json Result Code
defined('RESULT_OK')           || define('RESULT_OK', 1);
defined('RESULT_FAIL')         || define('RESULT_FAIL', 2);
defined('RESULT_STOP')         || define('RESULT_STOP', 3);
defined('RESULT_ERROR')        || define('RESULT_ERROR', 4);
defined('RESULT_EXIST_ID')     || define('RESULT_EXIST_ID', 6);
defined('RESULT_EXIST_NAME')   || define('RESULT_EXIST_NAME', 7);
defined('RESULT_MAINTAIN')     || define('RESULT_MAINTAIN', 8);
defined('RESULT_OVERMONEY')     || define('RESULT_OVERMONEY', 9);
defined('RESULT_OVERRATIO')     || define('RESULT_OVERRATIO', 10);
defined('RESULT_CAPTCHA_ERR')  || define('RESULT_CAPTCHA_ERR', 11);
defined('RESULT_CAPTCHA_NONE')  || define('RESULT_CAPTCHA_NONE', 12);

//Json Result Status
defined('STATUS_SUCCESS')      || define('STATUS_SUCCESS', 'success');
defined('STATUS_FAIL')         || define('STATUS_FAIL', 'fail');
defined('STATUS_LOGOUT')       || define('STATUS_LOGOUT', 'logout');

//
defined('TM_OFFSET')    	   || define('TM_OFFSET', 20);

//Game Type
defined('GAME_POWER_BALL')       || define('GAME_POWER_BALL', 0);   
defined('GAME_COIN5_BALL')       || define('GAME_COIN5_BALL', 1);
defined('GAME_BOGLE_BALL')       || define('GAME_BOGLE_BALL', 2);

//Site Config
defined('CONF_SITENAME')       || define('CONF_SITENAME', 1);
defined('CONF_MAINTAIN')       || define('CONF_MAINTAIN', 10);

//Money History
defined('MONEYCHANGE_CHARGE')    || define('MONEYCHANGE_CHARGE', 1);   
defined('MONEYCHANGE_EXCHANGE')  || define('MONEYCHANGE_EXCHANGE', 2);   
defined('MONEYCHANGE_PRESENT')   || define('MONEYCHANGE_PRESENT', 3); 
defined('MONEYCHANGE_RECOVERY')  || define('MONEYCHANGE_RECOVERY', 4); 
defined('MONEYCHANGE_BET')       || define('MONEYCHANGE_BET', 5);   
defined('MONEYCHANGE_WIN')       || define('MONEYCHANGE_WIN', 6);
defined('MONEYCHANGE_CANCEL')    || define('MONEYCHANGE_CANCEL', 7);
defined('POINTCHANGE_BET')       || define('POINTCHANGE_BET', 8);   
defined('POINTCHANGE_CANCEL')    || define('POINTCHANGE_CANCEL', 9);
defined('POINTCHANGE_EXCHANGE')  || define('POINTCHANGE_EXCHANGE', 10);
defined('MONEYCHANGE_CHARGE_FROM')    || define('MONEYCHANGE_CHARGE_FROM', 11);   
defined('MONEYCHANGE_EXCHANGE_TO')    || define('MONEYCHANGE_EXCHANGE_TO', 12);   
defined('MONEYCHANGE_PRESENT_FROM')   || define('MONEYCHANGE_PRESENT_FROM', 13); 
defined('MONEYCHANGE_RECOVERY_TO')  || define('MONEYCHANGE_RECOVERY_TO', 14); 

//Bet Status
defined('BET_WAIT')              || define('BET_WAIT', 0);   
defined('BET_LOSS')              || define('BET_LOSS', 1);
defined('BET_WIN')               || define('BET_WIN', 2);
defined('BET_CANCEL')            || define('BET_CANCEL', 3);


//Charge Status
defined('CHARGE_STATE_WAIT')            || define('CHARGE_STATE_WAIT', 0);   
defined('CHARGE_STATE_PERMIT')          || define('CHARGE_STATE_PERMIT', 1);
defined('CHARGE_STATE_REFUSE')          || define('CHARGE_STATE_REFUSE', 2);

//Charge Type
defined('CHARGE_TYPE_DEFAULT')         || define('CHARGE_TYPE_DEFAULT', 0);   
defined('CHARGE_TYPE_PRESENT')         || define('CHARGE_TYPE_PRESENT', 1);

//Notice Type
defined('NOTICE_TYPE_POP')         || define('NOTICE_TYPE_POP', 0);   
defined('NOTICE_TYPE_QNA')         || define('NOTICE_TYPE_QNA', 1);   
defined('NOTICE_TYPE_MSG')         || define('NOTICE_TYPE_MSG', 2);


defined('SITE_MASTER_NAME')         || define('SITE_MASTER_NAME', 'admin');


//captcha Type
defined('CAPTCHA_CLIENT')           || define('CAPTCHA_CLIENT', 0);
defined('CAPTCHA_ADMIN')            || define('CAPTCHA_ADMIN', 1);