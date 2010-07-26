<?php

/**
 * 反馈信息的数据模型类
 * @author jake
 * @version 1.0
 * @created 27-二月-2010 14:05:04
 */
class lib_feedback extends spModel
{

	/**
	 * 主键
	 */
	public $pk = 'fid';
	/**
	 * 表名
	 */
	public $table = 'feedback';

	var $linker = array( 
		array(
			'type' => 'hasone',  
			'map' => 'fromuser',  
			'mapkey' => 'fromuid',
			'fclass' => 'lib_user',
			'fkey' => 'uid',
			'enabled' => true,
		),
		array(
			'type' => 'hasone',  
			'map' => 'touser', 
			'mapkey' => 'touid',
			'fclass' => 'lib_user',
			'fkey' => 'uid',
			'enabled' => true,
		),
	);
}
?>