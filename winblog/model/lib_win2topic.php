<?php

/**
 * 微博与话题多对多关联的数据模型类
 * @author jake
 * @version 1.0
 * @created 03-三月-2010 13:46:51
 */
class lib_win2topic extends spModel
{

	/**
	 * 作为主键
	 */
	public $pk = 'wid';
	/**
	 * 表名
	 */
	public $table = 'win2topic';

}
?>