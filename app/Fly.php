<?php
/**==================================================
 * Fly.php
 * 2010-07-28
 * 启动类
 ===================================================*/
define('DS', DIRECTORY_SEPARATOR);
define('PS', PATH_SEPARATOR);
define('BP', dirname(dirname(__FILE__)));

class Fly {
	/**
	 * 前端控制器对象
	 */
	private static $_app;

	/**
	 * 模块路径
	 */
	private static $_modulePath;

	/**
	 * 默认模块
	 */
	private static $_defaultModules = 'default';
	/**
	 * 配置信息
	 */
	private static $_config;
	/**
	 * 数据库
	 */
	private static  $_db;

	
	/**
	 * 初始化程序环境
	 */
	public static function init() {
		/**
		 * 设置自动加载路径
		 */
		$paths[] = BP . DS . 'app' . DS . 'code' . DS . 'local';
		$paths[] = BP . DS . 'app' . DS . 'code' . DS . 'community';
		$paths[] = BP . DS . 'app' . DS . 'code' . DS . 'core';
		self::$_modulePath = $appPath = $paths;
		$paths[] = BP . DS . 'lib';
		$appPath = implode(PS, $paths);
		set_include_path($appPath);
		/**
		 * 加载函数库
		 */
		include_once "Fly/functions.php";
		include_once "Zend/Loader/Autoloader.php";
		//启动自己加载
		$autoloader = Zend_Loader_Autoloader::getInstance();
		//全部自动加载
		$autoloader->setFallbackAutoloader(true);
	}
	/**
	 * 设置 $_config
	 */
	public static function setConfig(array $config) {
		self::$_config = $config;
	}

	/**
	 * 获取
	 */
	 public static function getConfig() {
		 return self::$_config;
	 }
	 /**
	  * 保存$_config;
	  */
	 private static function saveConfig() {
		 $filePath = BP.DS.'data'.DS.'config.cache.php';
		 $file = @fopen(BP.DS.'data'.DS.'config.cache.php','w+');
		 $fileBody = "<?php \n";
		 $fileBody .= '$config = '.arrayTostr(self::$_config).';';
		 fwrite($file,$fileBody);
		 fclose($file);
	 }
	 /**
	 * 初始化 $_config;
	 */
	Public static function initConfig() {
		if(!DEVMODE && file_exists(BP.DS.'data'.DS.'config.cache.php')) {
			include_once BP.DS.'data'.DS.'config.cache.php';
			self::$_config = $config;
			unset($_config);
			return;
		}
		include_once 'config/config.php';
		$modulesConfigPath = BP.DS.'app'.DS.'config'.DS.'modules';
		$moduleConfigFiles = getDirFile($modulesConfigPath,'php');
		if($moduleConfigFiles) {
			foreach($moduleConfigFiles as $moduleConfigFile) {
				include $moduleConfigFile['path'];
			}
		}
		self::$_config = $config;
		unset($config);
		//加载模块配置
		$_config = self::$_config;
		$modules = array();
		foreach($_config['modules'] as $moduleName=>$module) {
			if(!$module['start']) continue;
			//模块路径
			$modulePath = BP . DS . 'app' . DS . 'code' . DS .$module['directory'];
			//加载模块配置
			if(file_exists($modulePath. DS .'config')) {
				$moduleConfigFiles = getDirFile($modulePath. DS .'config','php');
				if($moduleConfigFiles) {
					foreach($moduleConfigFiles as $moduleConfigFile) {
						include $moduleConfigFile['path'];
						if(isset($$moduleConfigFile['name'])) {
							if($moduleConfigFile['name'] != 'global') {
								$_config['modules'][$moduleName][$moduleConfigFile['name']] = $$moduleConfigFile['name'];
							}
							unset($$moduleConfigFile['name']);
						}
					}
				}
			}
			//加载模块
			if($module['url'] !== 'none') {
				$modules[$moduleName] = $modulePath .DS. 'controllers';
			}
		}
		$_config['app']['modules'] = $modules;
		self::$_config = $_config;
		//当不说开发模式时保存配置
		if(!DEVMODE) {
			self::saveConfig();
		}
		unset($_config);
	}

	/**
	 * 初始化数据库
	 */
	public static function initDb() {
		$db = self::$_config['db'];
		$params = array (
				 'host'     => $db['host'],
                 'username' => $db['username'],
                 'password' => $db['password'],
                 'dbname'   => $db['dbname'],
				 'charset'  => $db['charset']
			);
		  self::$_db = Zend_Db::factory('PDO_MYSQL', $params);
	}
	/**
	 * 获取数据库对象
	 */
	public static  function getDb() {
		if(self::$_db === null) {
			self::intDb();
		}
		return self::$_db;
	}
	/**
	 * 初始化 $_app
	 */
	Public static function initApp($defaultModule = 'default') {
		self::$_app = Zend_Controller_Front::getInstance();
		//设置加载模块
		self::$_app->setControllerDirectory(self::$_config['app']['modules']);
		//默认模块
		$defaultModule = isset(self::$_config['app']['modules'][$defaultModule])?$defaultModule:'default';
		self::$_app->setDefaultModule($defaultModule);
		//禁用视图
		self::$_app->setParam('noViewRenderer', true);
		self::$_app->setParam('prefixDefaultModule', true);
	}
	/**
	 * 运行程序
	 * @var string $defaultModules 默认模块
	 */
	public static function run($defaultModule = 'default') {
		self::init();
		self::initConfig();
		self::initDb();
		self::initApp($defaultModule);
		self::$_app->dispatch();
	}

}