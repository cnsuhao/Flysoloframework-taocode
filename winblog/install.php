<?php

if( true == @file_exists(APP_PATH.'/config.php') )exit();
$defaults = array(
	"UC_CONNECT" => "mysql",
	"UC_DB_HOST" => "localhost",
	"UC_DB_USER" => "root",
	"UC_DB_PASSWORD" => "",
	"UC_DB_DBNAME" => "test",
	"UC_DB_PREFIX" => "uc_",
	
	"UC_KEY" => "",
	"UC_API" => "",
	"UC_ID" => "",
	
	
	"WIN_DB_HOST" => "localhost",
	"WIN_DB_USER" => "root",
	"WIN_DB_PASSWORD" => "",
	"WIN_DB_DBNAME" => "test",
	"WIN_DB_PREFIX" => "win_",
		
		
	"WIN_SITENAME" => "WinBlog微博",
	"WIN_SITEINTRO" => "这是一个新的微博客网站",
	"WIN_COPYRIGHT" => "Copyright (C) WinBlog.",
	"WIN_SUBDOMAIN" => 0,
	"WIN_PATH_INFO" => 0,
		
		
	"WIN_USERNAME" => "admin",
	"WIN_NICKNAME" => "admin",
	"WIN_SEX" => 0,
	"WIN_EMAIL" => "",
	"WIN_PASSWORD" => ""
);
	
	
function ins_checkdblink($configs){
	global $dblink,$err;
	$dblink = mysql_connect($configs['WIN_DB_HOST'], $configs['WIN_DB_USER'], $configs['WIN_DB_PASSWORD']);
	if(false == $dblink){$err = '无法链接网站数据库，请检查网站数据库设置！';return false;}
	if(! mysql_select_db($configs['WIN_DB_DBNAME'], $dblink)){$err = '无法选择网站数据库，请确定网站数据库名称正确！'; return false;}
	ins_query("SET NAMES UTF8");
	return true;
}

function ins_query($sql,$prefix = ""){
	global $dblink,$err;
	$sqlarr = explode(";", $sql);
	foreach($sqlarr as $single){
		if( !empty($single) && strlen($single) > 5 ){
			$single = str_replace("\n",'',$single);
			$single = str_replace("#DBPREFIX#",$prefix,$single );
			if( !mysql_query($single, $dblink) ){$err = "数据库执行错误：".mysql_error();return false;}
		}
	}
}

function ins_registeruser($configs, $prefix = "")	{
	global $dblink,$err,$adminsql;
	define('UC_CONNECT', $configs['UC_CONNECT']);
	define('UC_DBHOST', $configs['UC_DB_HOST']);
	define('UC_DBUSER', $configs['UC_DB_USER']);				
	define('UC_DBPW', $configs['UC_DB_PASSWORD']);					
	define('UC_DBNAME', $configs['UC_DB_DBNAME']);	
	define('UC_DBCHARSET', 'utf8');
	define('UC_DBTABLEPRE', $configs['UC_DB_DBNAME'].'.'.$configs['UC_DB_PREFIX']);		
	
	define('UC_KEY', $configs['UC_KEY']);
	define('UC_API', $configs['UC_API']);
	define('UC_CHARSET',  'utf8');
	define('UC_IP', '');	
	define('UC_APPID', $configs['UC_ID']);	
	require(SP_PATH."/Extensions/uc_client/client.php");
	$password = substr(md5($configs["WIN_PASSWORD"]),0,20);
	$uid = uc_user_register($configs['WIN_USERNAME'], $password, $configs['WIN_EMAIL']);
	if($uid <= 0) {
		if($uid == -1) {
			$err = '用户名不合法';return false;
		} elseif($uid == -2) {
			$err = '包含要允许注册的词语';return false;
		} elseif($uid == -3) {
			$err = '用户名已经存在';return false;
		} elseif($uid == -4) {
			$err = 'Email 格式有误';return false;
		} elseif($uid == -5) {
			$err = 'Email 不允许注册';return false;
		} elseif($uid == -6) {
			$err = '该 Email 已经被注册';return false;
		} else {
			$err = '未定义';return false;
		}
	} else {
		$ctime = time();
		$adminsql = "INSERT INTO `{$prefix}user` (`uid`, `username`, `nickname`, `email`, `sex`, `ctime`, `acl_name`) VALUES ({$uid}, '{$configs[WIN_USERNAME]}', '{$configs[WIN_NICKNAME]}', '{$configs[WIN_EMAIL]}', '{$configs[WIN_SEX]}', '{$ctime}', 'WINADMIN');";
		return true;
	}

}

function ins_writeconfig($configs){
	$configex = file_get_contents(APP_PATH."/config-example.php");
	foreach( $configs as $skey => $value ){
		$skey = "#".$skey."#";
		$configex = str_replace($skey, $value, $configex);
	}
	file_put_contents (APP_PATH."/config.php" ,$configex);
}


$sql = "

DROP TABLE IF EXISTS #DBPREFIX#topic
;
DROP TABLE IF EXISTS #DBPREFIX#feedback
;
DROP TABLE IF EXISTS #DBPREFIX#fans
;
DROP TABLE IF EXISTS #DBPREFIX#badword
;
DROP TABLE IF EXISTS #DBPREFIX#acl
;
DROP TABLE IF EXISTS #DBPREFIX#win2topic
;
DROP TABLE IF EXISTS #DBPREFIX#win
;
DROP TABLE IF EXISTS #DBPREFIX#user
;

CREATE TABLE #DBPREFIX#topic
(
	tid int NOT NULL AUTO_INCREMENT,
	topic VARCHAR(200) NOT NULL,
	clicks BIGINT NOT NULL DEFAULT 0,
	PRIMARY KEY (tid)
) DEFAULT CHARSET utf8
;


CREATE TABLE #DBPREFIX#feedback
(
	fid int NOT NULL AUTO_INCREMENT,
	ctype CHAR(10) NOT NULL,
	fromuid MEDIUMINT NOT NULL,
	msg VARCHAR(200) NOT NULL,
	touid MEDIUMINT,
	ctime int NOT NULL,
	PRIMARY KEY (fid)
) DEFAULT CHARSET utf8
;


CREATE TABLE #DBPREFIX#fans
(
	fansid BIGINT NOT NULL AUTO_INCREMENT,
	fromuid MEDIUMINT NOT NULL,
	touid MEDIUMINT NOT NULL,
	PRIMARY KEY (fansid)
) DEFAULT CHARSET utf8
;


CREATE TABLE #DBPREFIX#badword
(
	bid int NOT NULL AUTO_INCREMENT,
	word VARCHAR(50) NOT NULL,
	ctype TINYINT NOT NULL DEFAULT 0,
	PRIMARY KEY (bid)
) DEFAULT CHARSET utf8
;


CREATE TABLE #DBPREFIX#acl
(
	aclid int NOT NULL AUTO_INCREMENT,
	name VARCHAR(200) NOT NULL,
	controller VARCHAR(50) NOT NULL,
	action VARCHAR(50) NOT NULL,
	acl_name VARCHAR(50) NOT NULL,
	PRIMARY KEY (aclid)
) DEFAULT CHARSET utf8
;


CREATE TABLE #DBPREFIX#win2topic
(
	wid MEDIUMINT NOT NULL,
	tid int NOT NULL
) 
;


CREATE TABLE #DBPREFIX#win
(
	wid MEDIUMINT NOT NULL AUTO_INCREMENT,
	username CHAR(15) NOT NULL,
	uid MEDIUMINT NOT NULL,
	nickname CHAR(15) NOT NULL,
	contents VARCHAR(255) NOT NULL,
	atuser CHAR(15),
	refrom VARCHAR(50) NOT NULL,
	repostid int,
	repostsum int NOT NULL DEFAULT 0,
	commentto int,
	commentsum int NOT NULL DEFAULT 0,
	ctime int NOT NULL,
	PRIMARY KEY (wid)
) DEFAULT CHARSET utf8
;

CREATE TABLE #DBPREFIX#user
(
	uid MEDIUMINT NOT NULL,
	username CHAR(15) NOT NULL,
	nickname CHAR(15) NOT NULL,
	sex int NOT NULL DEFAULT 1,
	city CHAR(10),
	blog VARCHAR(50),
	email CHAR(32),
	intro VARCHAR(210),
	ctime int NOT NULL,
	acl_name VARCHAR(50) NOT NULL,
	click MEDIUMINT NOT NULL DEFAULT 0,
	winsum MEDIUMINT NOT NULL DEFAULT 0,
	themes VARCHAR(15) NOT NULL DEFAULT 'default',
	pwhash VARCHAR(50),
	reminder VARCHAR(100),
	PRIMARY KEY (uid)
) DEFAULT CHARSET utf8
;



INSERT INTO `#DBPREFIX#acl` (`name`, `controller`, `action`, `acl_name`) VALUES ('反馈建议', 'feedback', 'idea', 'USERS');
INSERT INTO `#DBPREFIX#acl` (`name`, `controller`, `action`, `acl_name`) VALUES ('反馈举报', 'feedback', 'jubao', 'USERS');

INSERT INTO `#DBPREFIX#acl` (`name`, `controller`, `action`, `acl_name`) VALUES ('分享网址', 'widget', 'share', 'USERS');
INSERT INTO `#DBPREFIX#acl` (`name`, `controller`, `action`, `acl_name`) VALUES ('更换头像', 'panel', 'avatar', 'USERS');
INSERT INTO `#DBPREFIX#acl` (`name`, `controller`, `action`, `acl_name`) VALUES ('分享书签', 'panel', 'bookmark', 'USERS');
INSERT INTO `#DBPREFIX#acl` (`name`, `controller`, `action`, `acl_name`) VALUES ('修改密码', 'panel', 'chgpw', 'USERS');
INSERT INTO `#DBPREFIX#acl` (`name`, `controller`, `action`, `acl_name`) VALUES ('个人资料修改', 'panel', 'info', 'USERS');
INSERT INTO `#DBPREFIX#acl` (`name`, `controller`, `action`, `acl_name`) VALUES ('提醒设置', 'panel', 'reminder', 'USERS');
INSERT INTO `#DBPREFIX#acl` (`name`, `controller`, `action`, `acl_name`) VALUES ('模板设置', 'panel', 'template', 'USERS');
INSERT INTO `#DBPREFIX#acl` (`name`, `controller`, `action`, `acl_name`) VALUES ('博客挂件', 'panel', 'widget', 'USERS');

INSERT INTO `#DBPREFIX#acl` (`name`, `controller`, `action`, `acl_name`) VALUES ('我的关注', 'wblog', 'asfans', 'USERS');
INSERT INTO `#DBPREFIX#acl` (`name`, `controller`, `action`, `acl_name`) VALUES ('回复我的', 'wblog', 'atme', 'USERS');
INSERT INTO `#DBPREFIX#acl` (`name`, `controller`, `action`, `acl_name`) VALUES ('评论我的', 'wblog', 'comment', 'USERS');
INSERT INTO `#DBPREFIX#acl` (`name`, `controller`, `action`, `acl_name`) VALUES ('删除微博', 'wblog', 'del', 'USERS');
INSERT INTO `#DBPREFIX#acl` (`name`, `controller`, `action`, `acl_name`) VALUES ('查看关注', 'wblog', 'fans', 'USERS');
INSERT INTO `#DBPREFIX#acl` (`name`, `controller`, `action`, `acl_name`) VALUES ('微博首页', 'wblog', 'index', 'USERS');
INSERT INTO `#DBPREFIX#acl` (`name`, `controller`, `action`, `acl_name`) VALUES ('查看微博', 'wblog', 'win', 'USERS');
INSERT INTO `#DBPREFIX#acl` (`name`, `controller`, `action`, `acl_name`) VALUES ('我的微博', 'wblog', 'view', 'USERS');

INSERT INTO `#DBPREFIX#acl` (`name`, `controller`, `action`, `acl_name`) VALUES ('分享网址', 'widget', 'share', 'WINADMIN');
INSERT INTO `#DBPREFIX#acl` (`name`, `controller`, `action`, `acl_name`) VALUES ('更换头像', 'panel', 'avatar', 'WINADMIN');
INSERT INTO `#DBPREFIX#acl` (`name`, `controller`, `action`, `acl_name`) VALUES ('分享书签', 'panel', 'bookmark', 'WINADMIN');
INSERT INTO `#DBPREFIX#acl` (`name`, `controller`, `action`, `acl_name`) VALUES ('修改密码', 'panel', 'chgpw', 'WINADMIN');
INSERT INTO `#DBPREFIX#acl` (`name`, `controller`, `action`, `acl_name`) VALUES ('个人资料修改', 'panel', 'info', 'WINADMIN');
INSERT INTO `#DBPREFIX#acl` (`name`, `controller`, `action`, `acl_name`) VALUES ('提醒设置', 'panel', 'reminder', 'WINADMIN');
INSERT INTO `#DBPREFIX#acl` (`name`, `controller`, `action`, `acl_name`) VALUES ('模板设置', 'panel', 'template', 'WINADMIN');
INSERT INTO `#DBPREFIX#acl` (`name`, `controller`, `action`, `acl_name`) VALUES ('博客挂件', 'panel', 'widget', 'WINADMIN');

INSERT INTO `#DBPREFIX#acl` (`name`, `controller`, `action`, `acl_name`) VALUES ('我的关注', 'wblog', 'asfans', 'WINADMIN');
INSERT INTO `#DBPREFIX#acl` (`name`, `controller`, `action`, `acl_name`) VALUES ('回复我的', 'wblog', 'atme', 'WINADMIN');
INSERT INTO `#DBPREFIX#acl` (`name`, `controller`, `action`, `acl_name`) VALUES ('评论我的', 'wblog', 'comment', 'WINADMIN');
INSERT INTO `#DBPREFIX#acl` (`name`, `controller`, `action`, `acl_name`) VALUES ('删除微博', 'wblog', 'del', 'WINADMIN');
INSERT INTO `#DBPREFIX#acl` (`name`, `controller`, `action`, `acl_name`) VALUES ('查看关注', 'wblog', 'fans', 'WINADMIN');
INSERT INTO `#DBPREFIX#acl` (`name`, `controller`, `action`, `acl_name`) VALUES ('微博首页', 'wblog', 'index', 'WINADMIN');
INSERT INTO `#DBPREFIX#acl` (`name`, `controller`, `action`, `acl_name`) VALUES ('查看微博', 'wblog', 'win', 'WINADMIN');
INSERT INTO `#DBPREFIX#acl` (`name`, `controller`, `action`, `acl_name`) VALUES ('我的微博', 'wblog', 'view', 'WINADMIN');

INSERT INTO `#DBPREFIX#acl` (`name`, `controller`, `action`, `acl_name`) VALUES ('后台管理', 'admin', 'index', 'WINADMIN');
INSERT INTO `#DBPREFIX#acl` (`name`, `controller`, `action`, `acl_name`) VALUES ('反馈处理', 'admin', 'feedback', 'WINADMIN');
INSERT INTO `#DBPREFIX#acl` (`name`, `controller`, `action`, `acl_name`) VALUES ('关键词设置', 'admin', 'badword', 'WINADMIN');
INSERT INTO `#DBPREFIX#acl` (`name`, `controller`, `action`, `acl_name`) VALUES ('站点设置', 'admin', 'setting', 'WINADMIN');
INSERT INTO `#DBPREFIX#acl` (`name`, `controller`, `action`, `acl_name`) VALUES ('用户管理', 'adminuser', 'index', 'WINADMIN');
INSERT INTO `#DBPREFIX#acl` (`name`, `controller`, `action`, `acl_name`) VALUES ('删除用户', 'adminuser', 'del', 'WINADMIN');
";


if( empty($_GET["step"]) || 1 == $_GET["step"]  ){
	// 第一步，检查更新
	$checkurl = "http://speedphp.com/app/winblog/?v=" . WINBLOG_VERSION;
	require(APP_PATH.'/template/install/step1.html');
}elseif( 2 == $_GET["step"] ){
	// 第二步，填写资料
	$tips = $defaults;
	require(APP_PATH.'/template/install/step2.html');
}else{
	// 第三步，验证资料，写入资料，完成安装
	$dblink = null;$err=null;$adminsql = null;
	while(1){
		// 检查本地数据库设置
		ins_checkdblink($_POST);if( null != $err )break;
		// 增加远程UCenter用户
		ins_registeruser($_POST,$_POST["WIN_DB_PREFIX"]);if( null != $err )break;
		// 本地数据库入库
		$sql .= $adminsql;
		ins_query($sql,$_POST["WIN_DB_PREFIX"]);if( null != $err )break;
		// 改写本地配置文件
		ins_writeconfig($_POST);if( null != $err )break;
		break;
	}
	if( null != $err ){ // 有错误则覆盖
		$tips = array_merge($defaults, $_POST); // 显示原值或新值
		require(APP_PATH.'/template/install/step2.html');
	}else{
		require(APP_PATH.'/template/install/step3.html');
	}
}

	

	
