<?php
/**
 * 模块配置
 * 格式
 *	'模块名（英文）' => array(
 *		'title'		=> '模块标题',
 *		'directory'	=> '模块目录',
 *		'url'		=> 'default',
 *		'version'	=> '1.0',
 *		'start'		=> true
 *	)
 */
$config['modules'] = array(
	'default' => array(
		'title'		=> '默认模块',
		'directory'	=> 'core/default',
		'url'		=> 'default',
		'version'	=> '1.0',
		'start'		=> true
	),

	'member' => array(
		'title'		=> '用户中心',
		'directory' => 'core/member',
		'url'		=> 'member',
		'version'	=> '1.0',
		'start'		=> true
	)
);

