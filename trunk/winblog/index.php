<?php
define("SP_PATH",dirname(__FILE__)."/SpeedPHP");
define("APP_PATH",dirname(__FILE__));
define("WINBLOG_VERSION","1.1");
@date_default_timezone_set('PRC');

if( true != @file_exists(APP_PATH.'/config.php') ){require(APP_PATH.'/install.php');exit;}

require(APP_PATH.'/model/functions.php');
$spConfig = require(APP_PATH."/config.php");
require(SP_PATH."/SpeedPHP.php");