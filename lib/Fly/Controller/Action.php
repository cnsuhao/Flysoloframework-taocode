<?php
/**
 * Fly基础控制器
 */
class Fly_Controller_Action extends Zend_Controller_Action {
	/**
	 * 数据库
	 */
	public $db;
	public $view;
	/**
	 * 初始化
	 */
	public function init() {
		parent::init();
		$this->db = Fly::getDb();
		$this->_init();
	}
	/**
	 * 附加初始化
	 */
	public function _init() {
		
	}
	/**
	 * 初始化视图
	 */
	public function initView() {
		
	}
}