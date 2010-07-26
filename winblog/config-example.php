<?php
return array(	
'ext' => array(
	
	// 康盛UCenter的设置
	
	'spUcenter' => array(
		'UC_CONNECT' => '#UC_CONNECT#', 	// 连接 UCenter 的方式: mysql/NULL
									// mysql 是直接连接的数据库, 为了效率, 建议采用 mysql
		//数据库相关 (mysql 连接时, 并且没有设置 UC_DBLINK 时, 需要配置以下变量)
		'UC_DBHOST' => '#UC_DB_HOST#',	// UCenter 数据库主机
		'UC_DBUSER' => '#UC_DB_USER#',		// UCenter 数据库用户名
		'UC_DBPW'   => '#UC_DB_PASSWORD#',	// UCenter 数据库密码
		'UC_DBNAME' => '#UC_DB_DBNAME#',	// UCenter 数据库名称
		'UC_DBCHARSET' => 'utf8',			// UCenter 数据库字符集
		'UC_DBTABLEPRE' => '#UC_DB_DBNAME#.#UC_DB_PREFIX#',	// UCenter 数据库表前缀
	
		//通信相关
		'UC_KEY' => '#UC_KEY#', 				// 与 UCenter 的通信密钥, 要与 UCenter 保持一致
		'UC_API' => '#UC_API#', 	// UCenter 的 URL 地址, 在调用头像时依赖此常量
		'UC_CHARSET' => 'utf8', 	// UCenter 的字符集
		'UC_IP'  => '',  // UCenter 的 IP, 当 UC_CONNECT 为非 mysql 方式时, 并且当前应用服务器解析
		'UC_APPID' => '#UC_ID#'				// 当前应用的 ID
	),
		
),
	// 网站配置
	
'winblog_defined' => array(
	
	'sitename' => '#WIN_SITENAME#',  // 网站名称
	
	'siteintro' => '#WIN_SITEINTRO#',    // 网站介绍
	
	'template' => 'default',     // 默认模板
	
	'copyright' => '#WIN_COPYRIGHT#',   // 版权信息
	
	'subdomain' => '#WIN_SUBDOMAIN#',   // 是否开启二级域名
),	
	
	// 数据库配置
	
'db' => array(
	'host' => '#WIN_DB_HOST#',
	'login' => '#WIN_DB_USER#',
	'password' => '#WIN_DB_PASSWORD#',
	'database' => '#WIN_DB_DBNAME#',
	'prefix' => '#WIN_DB_PREFIX#'
),
	
	// PATH_INFO配置
		
'url' => array(
	// 是否使用path_info方式的URL
	'url_path_info' => '#WIN_PATH_INFO#', 
),
	
	// Smarty配置	

'view' => array(
	'enabled' => TRUE,
	'config' =>array(
		'template_dir' => APP_PATH.'/template',
		'compile_dir' => APP_PATH.'/tmp',
		'cache_dir' => APP_PATH.'/tmp',
		'left_delimiter' => '<{',
		'right_delimiter' => '}>',
	),
),
	// 系统配置	
'mode' => 'debug',
'launch' => array( 
	 'router_prefilter' => array( 
		array('lib_domain','enter'), // 二级域名转向
		array('spAcl','mincheck'), // 开启有限的权限控制
	 )
),
);

