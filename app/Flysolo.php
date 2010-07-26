<?php
/**
 * 文件名：Flysolo.php
 * 版权：Copyright 2010 WuTao. Co. Ltd. All Rights Reserved.
 * 描述：FlySolo主程序
 * 创建人：吴涛
 * 创建时间：2010-01-25 10:13
 * 类：Flysolo
 * 包：Flysolo_Core
 * 版本: 1.0V
 */
define('DS', DIRECTORY_SEPARATOR);
define('PS', PATH_SEPARATOR);
define('BP', dirname(dirname(__FILE__)));

Flysolo::register('original_include_path', get_include_path());

/**
 * 设置自动加载路径
 */
$paths[] = BP . DS . 'app' . DS . 'code' . DS . 'local';
$paths[] = BP . DS . 'app' . DS . 'code' . DS . 'community';
$paths[] = BP . DS . 'app' . DS . 'code' . DS . 'core';
$paths[] = BP . DS . 'lib';
$appPath = implode(PS, $paths);
set_include_path($appPath . PS . Flysolo::registry('original_include_path'));
/**
 * 加载函数库
 */
include_once "Flysolo/functions.php";
//自动载入
include_once "Flysolo/Autoload.php";
Flysolo_Autoload::register();
class Flysolo {
     /**
     * 注册表
     * @var array
     */
    static private $_registry                   = array();
    /**
     * 程序根路径
     */
    static private $_appRoot;
    /**
     * 程序实例
     * @var Flysolo_Core_Model_App
     */
    static private $_app;

    /**
     * 配置信息模型
     * @var Flysolo_Core_Model_Config
     */
    static private $_config;
    /**
     * 事件集合
     * @var Varien_Event_Collection
     */
    static private $_events;
     /**
     * 是否开启开发模式
     *
     * @var bool
     */
    static private $_isDeveloperMode            = false;

    /**
     * 设置程序绝对根路径
     * @param string $appRoot 路径 默认为空
     * @throws Mage_Core_Exception
     */
    public static function setRoot($appRoot = '')
    {
        if (self::$_appRoot) {
            return ;
        }

        if ('' === $appRoot) {
            // automagically find application root by dirname of Mage.php
            $appRoot = dirname(__FILE__);
        }

        $appRoot = realpath($appRoot);

        if (is_dir($appRoot) and is_readable($appRoot)) {
            self::$_appRoot = $appRoot;
        } else {
            self::throwException($appRoot . ' is not a directory or not readable by this user');
        }
    }
    /**
     * 获取程序绝对根路径
     *
     * @return string
     */
    public static function getRoot()
    {
        return self::$_appRoot;
    }
    /**
     * 注册一个新变量
     *
     * @param string $key 键
     * @param mixed $value 值
     * @param bool $graceful 是否抛出异常
     * @throws Flysolo_Core_Exception
     */
    public static function register($key, $value, $graceful = false)
    {
        if (isset(self::$_registry[$key])) {
            if ($graceful) {
                return;
            }
            self::throwException('Mage registry key "'.$key.'" already exists');
        }
        self::$_registry[$key] = $value;
    }
    /**
     * 获取注册表项值
     */
    public static function registry($key , $default = null) {
        if(isset(self::$_registry[$key])) {
            return self::$_registry[$key];
        } else {
            return $default;
        }
    }
    /**
     * 注销注册表项
     *
     * @param string $key
     */
    public static function unregister($key)
    {
        if (isset(self::$_registry[$key])) {
            if (is_object(self::$_registry[$key]) && (method_exists(self::$_registry[$key], '__destruct'))) {
                self::$_registry[$key]->__destruct();
            }
            unset(self::$_registry[$key]);
        }
    }
    /**
     * Throw Exception
     *
     * @param string $message
     * @param string $messageStorage
     */
    public static function throwException($message, $messageStorage = null)
    {
        if ($messageStorage && ($storage = self::getSingleton($messageStorage))) {
            $storage->addError($message);
        }
        throw new Exception($message);
    }
    /**
     * 开始运行
     */
    public static function run($code = '', $type = 'store', $options=array()) {
    	try {
            Flysolo_Profiler::start('Flysolo');
            self::setRoot();          
            Flysolo_Profiler::stop('Flysolo');
      } catch (Exception $e) {
			    echo "Caught exception: " . get_class($e) . "\n";
			    echo "Message: " . $e->getMessage() . "\n";
			    // 处理错误的代码
			}
    }
    /**
     * 获取是否是开发模式
     * @return bool
     */
    public static function getIsDeveloperMode()
    {
        return self::$_isDeveloperMode;
    }

    /**
     * 获取配置实例
     * @return Flysolo_Core_Model_Config
     */
    public static function getConfig()
    {
        return self::$_config;
    }
}
